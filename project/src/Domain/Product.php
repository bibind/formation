<?php

declare(strict_types=1);

namespace App\Domain;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity]
#[ORM\Table(name: 'products')]
#[Vich\Uploadable]
class Product
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 32)]
    private string $id;

    #[ORM\Column(type: 'string', length: 180)]
    private string $name;

    #[ORM\ManyToOne(targetEntity: Category::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Category $category;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private User $owner;

    #[ORM\Column(type: 'integer')]
    private int $stock;

    #[ORM\Column(type: 'integer')]
    private int $price;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $imageName = null;

    #[Vich\UploadableField(mapping: 'product_images', fileNameProperty: 'imageName')]
    private ?File $imageFile = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $updatedAt;

    public function __construct(
        string $id,
        string $name,
        Category $category,
        User $owner,
        int $stock,
        int $price,
        ?string $imageName = null
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->category = $category;
        $this->owner = $owner;
        $this->stock = $stock;
        $this->price = $price;
        $this->imageName = $imageName;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function id(): string
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function category(): Category
    {
        return $this->category;
    }

    public function owner(): User
    {
        return $this->owner;
    }

    public function stock(): int
    {
        return $this->stock;
    }

    public function setStock(int $stock): void
    {
        if ($stock < 0) {
            throw new \DomainException('stock_invalid');
        }
        $this->stock = $stock;
    }

    public function price(): int
    {
        return $this->price;
    }

    public function setPrice(int $price): void
    {
        if ($price < 0) {
            throw new \DomainException('price_invalid');
        }
        $this->price = $price;
    }

    public function setName(string $name): void
    {
        if ($name === '') {
            throw new \DomainException('name_invalid');
        }
        $this->name = $name;
    }

    public function setCategory(Category $category): void
    {
        $this->category = $category;
    }

    public function imageName(): ?string
    {
        return $this->imageName;
    }

    public function setImageName(?string $imageName): void
    {
        $this->imageName = $imageName;
    }

    public function setImageFile(?File $file): void
    {
        $this->imageFile = $file;
        if ($file !== null) {
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function decrementStock(int $qty): void
    {
        if ($qty <= 0) {
            throw new \InvalidArgumentException('qty_invalid');
        }
        if ($this->stock < $qty) {
            throw new \DomainException('stock_insufficient');
        }
        $this->stock -= $qty;
    }
}
