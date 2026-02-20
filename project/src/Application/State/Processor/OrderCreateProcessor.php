<?php

declare(strict_types=1);

namespace App\Application\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Application\Dto\OrderInput;
use App\Application\Dto\OrderOutput;
use App\Application\Service\OrderFactory;
use App\Domain\Order;
use App\Domain\OrderLine;
use App\Domain\Product;
use App\Infrastructure\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\DBAL\LockMode;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Bundle\SecurityBundle\Security;

final class OrderCreateProcessor implements ProcessorInterface
{
    public function __construct(
        private OrderFactory $factory,
        private EntityManagerInterface $em,
        private Security $security,
        private ProductRepository $productRepository,
    ) {}

    public function process($data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        if (!$data instanceof OrderInput) {
            return $data;
        }
        $user = $this->security->getUser();
        if (!$user instanceof \App\Domain\User) {
            throw new BadRequestHttpException('user_required');
        }

        $previous = $context['previous_data'] ?? null;
        if ($previous instanceof Order && $previous->status() !== Order::STATUS_DRAFT) {
            throw new \DomainException('order_not_draft');
        }

        if (count($data->lines) === 0) {
            throw new \DomainException('order_empty');
        }

        $productIds = array_values(array_unique(array_map(
            static fn (array $line): string => $line['productId'],
            $data->lines
        )));

        $this->em->beginTransaction();
        try {
            $productsById = $this->productRepository->findByIdsForUpdate($productIds, LockMode::PESSIMISTIC_WRITE);
            foreach ($data->lines as $line) {
                if ($line['quantity'] <= 0) {
                    throw new \DomainException('quantity_invalid');
                }
                $product = $productsById[$line['productId']] ?? null;
                if (!$product instanceof Product) {
                    throw new \DomainException('product_not_found');
                }
                $product->decrementStock($line['quantity']);
            }

            $order = $this->factory->fromInput($data, $user, $productsById);
            $order->validate();
            $this->em->persist($order);
            $this->em->flush();
            $this->em->commit();

            return $this->toOutput($order);
        } catch (\DomainException $e) {
            $this->em->rollback();
            throw new BadRequestHttpException($e->getMessage(), $e);
        } catch (\Throwable $e) {
            $this->em->rollback();
            throw $e;
        }
    }

    private function toOutput(Order $order): OrderOutput
    {
        $output = new OrderOutput();
        $output->id = $order->id();
        $output->total = $order->total();
        $output->lines = array_map(
            static function (OrderLine $line): array {
                return [
                    'name' => $line->product()->name(),
                    'quantity' => $line->quantity(),
                ];
            },
            $order->lines()
        );
        return $output;
    }
}
