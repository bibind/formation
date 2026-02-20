<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Application\Dto\ProductInput;
use App\Domain\Category;
use App\Domain\Product;
use App\Domain\User;

final class ProductFactory
{
    public function fromInput(ProductInput $input, User $owner, Category $category): Product
    {
        return new Product(
            id: $this->generateId(),
            name: $input->name,
            category: $category,
            owner: $owner,
            stock: $input->stock,
            price: $input->price,
        );
    }

    private function generateId(): string
    {
        return bin2hex(random_bytes(16));
    }
}
