<?php

declare(strict_types=1);

namespace App\Application\State\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Application\Dto\ProductOutput;
use App\Domain\Product;
use App\Infrastructure\Repository\ProductRepository;
use Symfony\Bundle\SecurityBundle\Security;

final class ProductCollectionProvider implements ProviderInterface
{
    public function __construct(private ProductRepository $repo, private Security $security) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): iterable
    {
        $context['user'] = $this->security->getUser();
        $products = $this->repo->fetchCollectionWithCategory($context);
        $outputs = [];
        foreach ($products as $product) {
            if ($product instanceof Product) {
                $outputs[] = $this->toOutput($product);
            }
        }
        return $outputs;
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
