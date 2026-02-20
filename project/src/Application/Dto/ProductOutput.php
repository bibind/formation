<?php

declare(strict_types=1);

namespace App\Application\Dto;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Application\State\Processor\ProductCreateProcessor;
use App\Application\State\Processor\ProductImageUploadProcessor;
use App\Application\State\Processor\ProductUpdateProcessor;
use App\Application\State\Provider\ProductCollectionProvider;

#[ApiResource(
    operations: [
        new GetCollection(uriTemplate: '/products', provider: ProductCollectionProvider::class),
        new Post(
            uriTemplate: '/products',
            input: ProductInput::class,
            processor: ProductCreateProcessor::class,
            read: false,
            security: "is_granted('ROLE_USER')"
        ),
        new Put(
            uriTemplate: '/products/{id}',
            input: ProductInput::class,
            processor: ProductUpdateProcessor::class,
            read: false,
            security: "is_granted('ROLE_USER')"
        ),
        new Post(
            uriTemplate: '/products/{id}/image',
            input: ProductImageUploadInput::class,
            processor: ProductImageUploadProcessor::class,
            output: false,
            deserialize: false,
            inputFormats: ['multipart' => ['multipart/form-data']],
            security: "is_granted('ROLE_USER')"
        ),
    ]
)]
final class ProductOutput
{
    public string $id;
    public string $name;
    public string $categoryName;
    public int $stock;
    public int $price;
    public ?string $imageUrl = null;
}
