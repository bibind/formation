<?php

declare(strict_types=1);

namespace App\Application\State\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Application\Dto\OrderOutput;
use App\Domain\Order;
use App\Domain\OrderLine;
use App\Infrastructure\Repository\OrderRepository;
use Symfony\Bundle\SecurityBundle\Security;

final class OrderCollectionProvider implements ProviderInterface
{
    public function __construct(private OrderRepository $repo, private Security $security) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): iterable
    {
        $context['user'] = $this->security->getUser();
        $orders = $this->repo->fetchOrdersWithLines($context);
        $outputs = [];
        foreach ($orders as $order) {
            if ($order instanceof Order) {
                $outputs[] = $this->toOutput($order);
            }
        }
        return $outputs;
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
