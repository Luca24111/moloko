<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Food
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 140)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 7, scale: 2)]
    private ?string $price = null;

    #[ORM\Column(options: ['default' => false])]
    private bool $isSpecial = false;

    #[ORM\Column(options: ['default' => true])]
    private bool $isEnabled = true;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imageUrl = null;

    #[ORM\ManyToOne(targetEntity: FoodCategory::class, inversedBy: 'foods')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?FoodCategory $foodCategory = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $updatedAt;

    public function __construct()
    {
        $now = new \DateTimeImmutable();
        $this->createdAt = $now;
        $this->updatedAt = $now;
    }

    public function __toString(): string
    {
        return $this->name ?? 'Piatto';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = trim($name);
        $this->touch();

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;
        $this->touch();

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): static
    {
        $this->price = $price;
        $this->touch();

        return $this;
    }

    public function isSpecial(): bool
    {
        return $this->isSpecial;
    }

    public function setIsSpecial(bool $isSpecial): static
    {
        $this->isSpecial = $isSpecial;
        $this->touch();

        return $this;
    }

    public function isEnabled(): bool
    {
        return $this->isEnabled;
    }

    public function setIsEnabled(bool $isEnabled): static
    {
        $this->isEnabled = $isEnabled;
        $this->touch();

        return $this;
    }

    public function getImageUrl(): ?string
    {
        return self::normalizeStoredImagePath($this->imageUrl);
    }

    public function setImageUrl(?string $imageUrl): static
    {
        $this->imageUrl = self::normalizeStoredImagePath($imageUrl);
        $this->touch();

        return $this;
    }

    public function getFoodCategory(): ?FoodCategory
    {
        return $this->foodCategory;
    }

    public function setFoodCategory(?FoodCategory $foodCategory): static
    {
        $this->foodCategory = $foodCategory;
        $this->touch();

        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    private function touch(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    private static function normalizeStoredImagePath(?string $imageUrl): ?string
    {
        $path = trim((string) $imageUrl);
        if ($path === '') {
            return null;
        }

        if ((bool) preg_match('#^(?:https?:)?//#i', $path) || str_starts_with($path, 'data:')) {
            return $path;
        }

        $normalizedPath = ltrim(
            preg_replace('#^(?:/?public)?/?uploads/media/foods/#', '', str_replace('\\', '/', $path)) ?? $path,
            '/'
        );

        return basename($normalizedPath);
    }
}
