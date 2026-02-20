<?php

declare(strict_types=1);

namespace App\Domain;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'orders')]
class Order
{
    public const STATUS_DRAFT = 'draft';
    public const STATUS_VALIDATED = 'validated';

    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 32)]
    private string $id;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private User $buyer;

    #[ORM\Column(type: 'string', length: 20)]
    private string $status = self::STATUS_DRAFT;

    #[ORM\Column(type: 'integer')]
    private int $total = 0;

    /** @var Collection<int, OrderLine> */
    #[ORM\OneToMany(mappedBy: 'order', targetEntity: OrderLine::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection $lines;

    public function __construct(string $id, User $buyer)
    {
        $this->id = $id;
        $this->buyer = $buyer;
        $this->lines = new ArrayCollection();
    }

    public function addLine(OrderLine $line): void
    {
        $this->assertDraft();
        $this->lines->add($line);
        $this->total += $line->subtotal();
    }

    public function id(): string
    {
        return $this->id;
    }

    public function lines(): array
    {
        return $this->lines->toArray();
    }

    public function buyer(): User
    {
        return $this->buyer;
    }

    public function total(): int
    {
        return $this->total;
    }

    public function status(): string
    {
        return $this->status;
    }

    public function validate(): void
    {
        $this->assertDraft();
        $this->status = self::STATUS_VALIDATED;
    }

    private function assertDraft(): void
    {
        if ($this->status !== self::STATUS_DRAFT) {
            throw new \DomainException('order_not_draft');
        }
    }
}
