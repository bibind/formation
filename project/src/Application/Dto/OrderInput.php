<?php

declare(strict_types=1);

namespace App\Application\Dto;

final class OrderInput
{
    /** @var array<int, array{productId: string, quantity: int}> */
    public array $lines = [];
}
