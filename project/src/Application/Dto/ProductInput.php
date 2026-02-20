<?php

declare(strict_types=1);

namespace App\Application\Dto;

final class ProductInput
{
    public string $name;
    public string $categoryId;
    public int $stock;
    public int $price;
}
