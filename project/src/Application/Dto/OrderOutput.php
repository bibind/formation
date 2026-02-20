<?php

declare(strict_types=1);

namespace App\Application\Dto;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Application\State\Processor\OrderCreateProcessor;
use App\Application\State\Provider\OrderCollectionProvider;

#[ApiResource(
    operations: [
        new GetCollection(uriTemplate: '/orders', provider: OrderCollectionProvider::class),
        new Post(
            uriTemplate: '/orders',
            input: OrderInput::class,
            processor: OrderCreateProcessor::class,
            read: false,
            security: "is_granted('ROLE_USER')"
        ),
    ]
)]
final class OrderOutput
{
    public string $id;
    public int $total;
    /** @var array<int, array{name: string, quantity: int}> */
    public array $lines = [];
}
