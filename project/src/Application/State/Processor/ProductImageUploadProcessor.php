<?php

declare(strict_types=1);

namespace App\Application\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Application\Dto\ProductImageUploadInput;
use App\Domain\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Bundle\SecurityBundle\Security;

final class ProductImageUploadProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private Security $security,
        private RequestStack $requestStack,
    ) {}

    public function process($data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        if (!$data instanceof ProductImageUploadInput) {
            $data = new ProductImageUploadInput();
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
            throw new AccessDeniedHttpException('product_edit_forbidden');
        }
        $file = $data->file;
        if ($file === null) {
            $request = $context['request'] ?? $this->requestStack->getCurrentRequest();
            if ($request) {
                $file = $request->files->get('file');
            }
        }
        if ($file === null) {
            throw new BadRequestHttpException('file_missing');
        }

        $maxSize = 2 * 1024 * 1024;
        if ($file->getSize() !== null && $file->getSize() > $maxSize) {
            throw new BadRequestHttpException('image_too_large');
        }
        $clientMime = $file->getClientMimeType();
        $mime = $file->getMimeType() ?? '';
        $allowed = ['image/jpeg', 'image/png', 'image/webp'];
        if (!in_array((string) $clientMime, $allowed, true) && !in_array($mime, $allowed, true)) {
            throw new BadRequestHttpException('image_invalid_mime');
        }

        $product->setImageFile($file);
        $this->em->flush();
        return $product;
    }
}
