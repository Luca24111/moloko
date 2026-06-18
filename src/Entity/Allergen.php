<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Allergen
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 120, unique: true)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    /** @var Collection<int, Ingredienti> */
    #[ORM\ManyToMany(targetEntity: Ingredienti::class, mappedBy: 'allergens')]
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
        $this->ingredienti = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->name ?? 'Allergene';
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
        $name = $name !== null ? trim($name) : null;
        $this->name = $name !== '' ? $name : null;
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

    /**
     * @return Collection<int, Ingredienti>
     */
    public function getIngredienti(): Collection
    {
        return $this->ingredienti;
    }

    public function addIngredienti(Ingredienti $ingredienti): static
    {
        if (!$this->ingredienti->contains($ingredienti)) {
            $this->ingredienti->add($ingredienti);
            $ingredienti->addAllergen($this);
        }

        return $this;
    }

    public function removeIngredienti(Ingredienti $ingredienti): static
    {
        if ($this->ingredienti->removeElement($ingredienti)) {
            $ingredienti->removeAllergen($this);
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
