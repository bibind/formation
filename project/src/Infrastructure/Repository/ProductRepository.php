<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Product;
use App\Domain\User;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;

final class ProductRepository
{
    public function __construct(private EntityManagerInterface $em) {}

    public function fetchCollectionWithCategory(array $context): iterable
    {
        $filters = $context['filters'] ?? [];
        $user = $context['user'] ?? null;
        $isAdmin = $user instanceof User && $user->isAdmin();

        $qb = $this->em->getRepository(Product::class)->createQueryBuilder('p')
            ->leftJoin('p.category', 'c')->addSelect('c')
            ->leftJoin('p.owner', 'o')->addSelect('o');

        if (!empty($filters['name'])) {
            $qb->andWhere('p.name LIKE :name')->setParameter('name', '%' . $filters['name'] . '%');
        }
        if (!empty($filters['category'])) {
            $qb->andWhere('c.id = :category')->setParameter('category', $filters['category']);
        }
        if (!$isAdmin && $user instanceof User) {
            $qb->andWhere('p.owner = :owner')->setParameter('owner', $user);
        } elseif (!empty($filters['owner']) && $filters['owner'] === 'current' && $user instanceof User) {
            $qb->andWhere('p.owner = :owner')->setParameter('owner', $user);
        }

        $page = max(1, (int)($filters['page'] ?? 1));
        $itemsPerPage = max(1, (int)($filters['itemsPerPage'] ?? 30));
        $qb->setFirstResult(($page - 1) * $itemsPerPage)->setMaxResults($itemsPerPage);

        return new Paginator($qb, true);
    }

    /**
     * @param string[] $ids
     * @return array<string, Product>
     */
    public function findByIdsForUpdate(array $ids, int|\Doctrine\DBAL\LockMode $lockMode): array
    {
        $qb = $this->em->getRepository(Product::class)->createQueryBuilder('p')
            ->where('p.id IN (:ids)')->setParameter('ids', $ids);

        $query = $qb->getQuery()->setLockMode($lockMode);
        $result = $query->getResult();

        $byId = [];
        foreach ($result as $product) {
            $byId[$product->id()] = $product;
        }
        return $byId;
    }
}
