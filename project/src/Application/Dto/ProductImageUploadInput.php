<?php

declare(strict_types=1);

namespace App\Application\Dto;

use Symfony\Component\HttpFoundation\File\UploadedFile;

final class ProductImageUploadInput
{
    public ?UploadedFile $file = null;
}
