<?php

declare(strict_types=1);

namespace App\Application\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Application\Dto\ProductInput;
use App\Application\Dto\ProductOutput;
use App\Domain\Category;
use App\Domain\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class ProductUpdateProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private Security $security,
        private ValidatorInterface $validator,
    ) {}

    public function process($data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        if (!$data instanceof ProductInput) {
            return $data;
        }
        $productId = $uriVariables['id'] ?? null;
        if (!is_string($productId) || $productId === '') {
            throw new BadRequestHttpException('product_id_missing');
        }
        $product = $this->em->getRepository(Product::class)->find($productId);
        if (!$product instanceof Product) {
            throw new NotFoundHttpException('product_not_found');
        }
        if (!$this->security->isGranted('PRODUCT_EDIT', $product)) {
            throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException('product_edit_forbidden');
        }
        $category = $this->em->getRepository(Category::class)->find($data->categoryId);
        if (!$category instanceof Category) {
            throw new BadRequestHttpException('category_not_found');
        }

        $product->setName($data->name);
        $product->setCategory($category);
        $product->setStock($data->stock);
        $product->setPrice($data->price);
        $this->validator->validate($product);
        $this->em->flush();

        return $this->toOutput($product);
    }

    private function toOutput(Product $product): ProductOutput
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
