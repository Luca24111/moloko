<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Food
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 140, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(nullable: true, options: ['default' => 0])]
    private ?int $displayOrder = 0;

    #[ORM\Column(options: ['default' => true])]
    private bool $isEnabled = true;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imageUrl = null;

    #[ORM\ManyToOne(targetEntity: FoodCategory::class, inversedBy: 'foods')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?FoodCategory $foodCategory = null;

    /** @var Collection<int, FoodCategory> */
    #[ORM\ManyToMany(targetEntity: FoodCategory::class, inversedBy: 'ingredientCategories')]
    #[ORM\JoinTable(name: 'ingredient_category_food_category')]
    private Collection $foodCategories;

    /** @var Collection<int, Ingredienti> */
    #[ORM\OneToMany(mappedBy: 'ingredientCategory', targetEntity: Ingredienti::class)]
    private Collection $ingredienti;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $updatedAt;

    public function __construct()
    {
        $now = new \DateTimeImmutable();
        $this->createdAt = $now;
        $this->updatedAt = $now;
        $this->foodCategories = new ArrayCollection();
        $this->ingredienti = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->name ?? 'Categoria ingredienti';
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

    /**
     * @return Collection<int, FoodCategory>
     */
    public function getFoodCategories(): Collection
    {
        return $this->foodCategories;
    }

    public function addFoodCategory(FoodCategory $foodCategory): static
    {
        if (!$this->foodCategories->contains($foodCategory)) {
            $this->foodCategories->add($foodCategory);
            $foodCategory->addIngredientCategory($this);
            $this->touch();
        }

        return $this;
    }

    public function removeFoodCategory(FoodCategory $foodCategory): static
    {
        if ($this->foodCategories->removeElement($foodCategory)) {
            $foodCategory->removeIngredientCategory($this);
            $this->touch();
        }

        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @return Collection<int, Ingredienti>
     */
    public function getIngredienti(): Collection
    {
        return $this->ingredienti;
    }

    public function addIngrediente(Ingredienti $ingrediente): static
    {
        if (!$this->ingredienti->contains($ingrediente)) {
            $this->ingredienti->add($ingrediente);
            $ingrediente->setIngredientCategory($this);
        }

        return $this;
    }

    public function removeIngrediente(Ingredienti $ingrediente): static
    {
        if ($this->ingredienti->removeElement($ingrediente)) {
            if ($ingrediente->getIngredientCategory() === $this) {
                $ingrediente->setIngredientCategory(null);
            }
        }

        return $this;
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
