<?php

declare(strict_types=1);

namespace App\Application\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Application\Dto\ProductInput;
use App\Application\Dto\ProductOutput;
use App\Application\Service\ProductFactory;
use App\Domain\Category;
use App\Domain\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class ProductCreateProcessor implements ProcessorInterface
{
    public function __construct(
        private ProductFactory $factory,
        private EntityManagerInterface $em,
        private Security $security,
        private ValidatorInterface $validator,
    ) {}

    public function process($data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        if (!$data instanceof ProductInput) {
            return $data;
        }
        $user = $this->security->getUser();
        if (!$user instanceof User) {
            throw new BadRequestHttpException('user_required');
        }
        if ($data->stock < 0 || $data->price < 0) {
            throw new \DomainException('product_values_invalid');
        }
        $category = $this->em->getRepository(Category::class)->find($data->categoryId);
        if (!$category instanceof Category) {
            throw new BadRequestHttpException('category_not_found');
        }
        $product = $this->factory->fromInput($data, $user, $category);
        $this->validator->validate($product);
        $this->em->persist($product);
        $this->em->flush();
        return $this->toOutput($product);
    }

    private function toOutput(\App\Domain\Product $product): ProductOutput
    {
        $output = new ProductOutput();
        $output->id = $product->id();
        $output->name = $product->name();
        $output->categoryName = $product->category()->name();
        $output->stock = $product->stock();
        $output->price = $product->price();
        $output->imageUrl = $product->imageName();
        return $output;
    }
}
