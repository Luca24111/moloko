<?php

namespace App\Service;

use App\Entity\Drink;
use App\Entity\DrinkCategory;
use App\Entity\Food;
use App\Entity\FoodCategory;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;

final class FrontendMenuProvider
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function getCategories(): array
    {
        $categories = $this->entityManager
            ->getRepository(DrinkCategory::class)
            ->findBy(['isActive' => true], ['displayOrder' => 'ASC', 'name' => 'ASC']);

        return array_map(fn (DrinkCategory $category): array => $this->mapCategory($category), $categories);
    }

    public function getGroupedDrinks(): array
    {
        $categories = $this->entityManager
            ->getRepository(DrinkCategory::class)
            ->findBy(['isActive' => true], ['displayOrder' => 'ASC', 'name' => 'ASC']);

        $groups = [];
        foreach ($categories as $category) {
            $drinks = $this->createEnabledDrinkQueryBuilder()
                ->andWhere('drink.drinkCategory = :category')
                ->setParameter('category', $category)
                ->orderBy('drink.name', 'ASC')
                ->getQuery()
                ->getResult();

            $groups[] = [
                'category' => $this->mapCategory($category),
                'drinks' => array_map(fn (Drink $drink): array => $this->mapDrink($drink), $drinks),
            ];
        }

        return $groups;
    }

    public function getSpecialDrinks(int $limit = 0): array
    {
        $queryBuilder = $this->createEnabledDrinkQueryBuilder()
            ->andWhere('drink.isSpecial = :isSpecial')
            ->setParameter('isSpecial', true)
            ->orderBy('drink.updatedAt', 'DESC')
            ->addOrderBy('drink.id', 'DESC');

        if ($limit > 0) {
            $queryBuilder->setMaxResults($limit);
        }

        $drinks = $queryBuilder->getQuery()->getResult();

        return array_map(fn (Drink $drink): array => $this->mapDrink($drink), $drinks);
    }

    public function countDrinks(): int
    {
        return $this->entityManager->getRepository(Drink::class)->count(['isEnabled' => true]);
    }

    public function countCategories(): int
    {
        return $this->entityManager->getRepository(DrinkCategory::class)->count(['isActive' => true]);
    }

    public function getFoods(): array
    {
        $foods = $this->entityManager
            ->getRepository(Food::class)
            ->createQueryBuilder('food')
            ->leftJoin('food.foodCategory', 'category')
            ->addSelect('category')
            ->andWhere('food.isEnabled = :isEnabled')
            ->andWhere('category.id IS NULL OR category.isActive = :categoryActive')
            ->setParameter('isEnabled', true)
            ->setParameter('categoryActive', true)
            ->orderBy('food.isSpecial', 'DESC')
            ->addOrderBy('category.displayOrder', 'ASC')
            ->addOrderBy('food.name', 'ASC')
            ->getQuery()
            ->getResult();

        return array_map(fn (Food $food): array => $this->mapFood($food), $foods);
    }

    public function getFoodCategories(): array
    {
        $categories = $this->entityManager
            ->getRepository(FoodCategory::class)
            ->findBy(['isActive' => true], ['displayOrder' => 'ASC', 'name' => 'ASC']);

        return array_map(fn (FoodCategory $category): array => $this->mapFoodCategory($category), $categories);
    }

    public function getGroupedFoods(): array
    {
        $categories = $this->entityManager
            ->getRepository(FoodCategory::class)
            ->findBy(['isActive' => true], ['displayOrder' => 'ASC', 'name' => 'ASC']);

        $groups = [];
        foreach ($categories as $category) {
            $foods = $this->createEnabledFoodQueryBuilder()
                ->andWhere('food.foodCategory = :foodCategory')
                ->setParameter('foodCategory', $category)
                ->orderBy('food.isSpecial', 'DESC')
                ->addOrderBy('food.name', 'ASC')
                ->getQuery()
                ->getResult();

            $groups[] = [
                'category' => $this->mapFoodCategory($category),
                'foods' => array_map(fn (Food $food): array => $this->mapFood($food), $foods),
            ];
        }

        return $groups;
    }

    private function mapCategory(DrinkCategory $category): array
    {
        $id = $category->getId() ?? 0;

        return [
            'slug' => 'cat-'.$id,
            'label' => $category->getName() ?? 'Categoria',
            'description' => $category->getDescription() ?? 'Categoria drink personalizzata dal pannello admin.',
        ];
    }

    private function mapDrink(Drink $drink): array
    {
        $category = $drink->getDrinkCategory();
        $categoryName = $category?->getName() ?? 'Senza categoria';

        return [
            'name' => $drink->getName() ?? 'Drink',
            'description' => $drink->getDescription() ?? 'Descrizione drink non disponibile.',
            'price' => number_format((float) ($drink->getPrice() ?? 0), 2, '.', ''),
            'category' => strtolower($categoryName),
            'category_slug' => $category !== null ? 'cat-'.($category->getId() ?? 0) : 'uncategorized',
            'image' => $drink->getImageUrl() ?: 'https://images.unsplash.com/photo-1514361892635-6f5b4d1cd4be?auto=format&fit=crop&w=900&q=80',
            'is_special' => $drink->isSpecial(),
            'is_enabled' => $drink->isEnabled(),
        ];
    }

    private function mapFood(Food $food): array
    {
        $category = $food->getFoodCategory();
        $categoryLabel = $category?->getName() ?? 'Senza categoria';

        return [
            'name' => $food->getName() ?? 'Piatto',
            'description' => $food->getDescription() ?? 'Descrizione piatto non disponibile.',
            'price' => number_format((float) ($food->getPrice() ?? 0), 2, '.', ''),
            'category' => strtolower($categoryLabel),
            'category_slug' => $category !== null ? 'food-cat-'.($category->getId() ?? 0) : 'food-uncategorized',
            'image' => $food->getImageUrl() ?: 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?auto=format&fit=crop&w=900&q=80',
            'is_special' => $food->isSpecial(),
            'is_enabled' => $food->isEnabled(),
        ];
    }

    private function mapFoodCategory(FoodCategory $category): array
    {
        $id = $category->getId() ?? 0;

        return [
            'slug' => 'food-cat-'.$id,
            'label' => $category->getName() ?? 'Categoria',
            'description' => $category->getDescription() ?? 'Categoria cibo configurata dal pannello admin.',
        ];
    }

    private function createEnabledDrinkQueryBuilder(): QueryBuilder
    {
        return $this->entityManager
            ->getRepository(Drink::class)
            ->createQueryBuilder('drink')
            ->leftJoin('drink.drinkCategory', 'category')
            ->addSelect('category')
            ->andWhere('drink.isEnabled = :isEnabled')
            ->setParameter('isEnabled', true);
    }

    private function createEnabledFoodQueryBuilder(): QueryBuilder
    {
        return $this->entityManager
            ->getRepository(Food::class)
            ->createQueryBuilder('food')
            ->leftJoin('food.foodCategory', 'category')
            ->addSelect('category')
            ->andWhere('food.isEnabled = :isEnabled')
            ->setParameter('isEnabled', true);
    }
}
