<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class FoodCategory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 120, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(nullable: true, options: ['default' => 0])]
    private ?int $displayOrder = 0;

    #[ORM\Column(options: ['default' => true])]
    private bool $isActive = true;

    /** @var Collection<int, Food> */
    #[ORM\OneToMany(mappedBy: 'foodCategory', targetEntity: Food::class)]
    private Collection $foods;

    /** @var Collection<int, Food> */
    #[ORM\ManyToMany(targetEntity: Food::class, mappedBy: 'foodCategories')]
    private Collection $ingredientCategories;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $updatedAt;

    public function __construct()
    {
        $now = new \DateTimeImmutable();
        $this->createdAt = $now;
        $this->updatedAt = $now;
        $this->foods = new ArrayCollection();
        $this->ingredientCategories = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->name ?? 'Categoria cibo';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name !== null ? trim($name) : null;
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

    public function getDisplayOrder(): ?int
    {
        return $this->displayOrder;
    }

    public function setDisplayOrder(?int $displayOrder): static
    {
        $this->displayOrder = $displayOrder;
        $this->touch();

        return $this;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;
        $this->touch();

        return $this;
    }

    /**
     * @return Collection<int, Food>
     */
    public function getFoods(): Collection
    {
        return $this->foods;
    }

    public function addFood(Food $food): static
    {
        if (!$this->foods->contains($food)) {
            $this->foods->add($food);
            $food->setFoodCategory($this);
        }

        return $this;
    }

    public function removeFood(Food $food): static
    {
        if ($this->foods->removeElement($food)) {
            if ($food->getFoodCategory() === $this) {
                $food->setFoodCategory(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Food>
     */
    public function getIngredientCategories(): Collection
    {
        return $this->ingredientCategories;
    }

    public function addIngredientCategory(Food $ingredientCategory): static
    {
        if (!$this->ingredientCategories->contains($ingredientCategory)) {
            $this->ingredientCategories->add($ingredientCategory);
            $ingredientCategory->addFoodCategory($this);
        }

        return $this;
    }

    public function removeIngredientCategory(Food $ingredientCategory): static
    {
        if ($this->ingredientCategories->removeElement($ingredientCategory)) {
            $ingredientCategory->removeFoodCategory($this);
        }

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
}
