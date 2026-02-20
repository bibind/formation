<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Order;
use App\Domain\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;

final class OrderRepository
{
    public function __construct(private EntityManagerInterface $em) {}

    public function fetchOrdersWithLines(array $context): iterable
    {
        $filters = $context['filters'] ?? [];
        $user = $context['user'] ?? null;
        $isAdmin = $user instanceof User && $user->isAdmin();

        $qb = $this->em->getRepository(Order::class)->createQueryBuilder('o')
            ->leftJoin('o.lines', 'l')->addSelect('l')
            ->leftJoin('l.product', 'p')->addSelect('p')
            ->leftJoin('o.buyer', 'b')->addSelect('b');

        if (!$isAdmin && $user instanceof User) {
            $qb->andWhere('o.buyer = :buyer')->setParameter('buyer', $user);
        }
        if (!empty($filters['status'])) {
            $qb->andWhere('o.status = :status')->setParameter('status', $filters['status']);
        }

        $page = max(1, (int)($filters['page'] ?? 1));
        $itemsPerPage = max(1, (int)($filters['itemsPerPage'] ?? 30));
        $qb->setFirstResult(($page - 1) * $itemsPerPage)->setMaxResults($itemsPerPage);

        return new Paginator($qb, true);
    }
}
