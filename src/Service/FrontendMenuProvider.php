<?php

namespace App\Service;

use App\Entity\Allergen;
use App\Entity\Drink;
use App\Entity\DrinkCategory;
use App\Entity\Event;
use App\Entity\Food;
use App\Entity\FoodCategory;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;

final class FrontendMenuProvider
{
    private const IMAGE_CATEGORY_LABELS = ['aperitivo', 'lista drink'];

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ManagedMediaStorage $mediaStorage
    ) {
    }

    public function getCategories(): array
    {
        $categories = $this->entityManager
            ->getRepository(DrinkCategory::class)
            ->findBy([], ['displayOrder' => 'ASC', 'name' => 'ASC']);

        $this->sortDrinkCategoriesForFrontend($categories);

        return array_map(fn (DrinkCategory $category): array => $this->mapCategory($category), $categories);
    }

    public function getGroupedDrinks(): array
    {
        $categories = $this->entityManager
            ->getRepository(DrinkCategory::class)
            ->findBy([], ['displayOrder' => 'ASC', 'name' => 'ASC']);

        $this->sortDrinkCategoriesForFrontend($categories);

        $drinks = $this->createEnabledDrinkQueryBuilder()
            ->orderBy('category.displayOrder', 'ASC')
            ->addOrderBy('drink.name', 'ASC')
            ->getQuery()
            ->getResult();

        return $this->groupDrinksByCategory($categories, $drinks);
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

    public function getPublishedEvents(int $limit = 0): array
    {
        $queryBuilder = $this->createPublishedEventQueryBuilder()
            ->addSelect(
                "CASE
                    WHEN event.startsAt IS NULL THEN 1
                    WHEN COALESCE(event.endsAt, event.startsAt) >= :today THEN 0
                    ELSE 2
                END AS HIDDEN eventSortBucket"
            )
            ->setParameter('today', new \DateTimeImmutable('today'))
            ->orderBy('eventSortBucket', 'ASC')
            ->addOrderBy('event.startsAt', 'ASC')
            ->addOrderBy('event.updatedAt', 'DESC')
            ->addOrderBy('event.id', 'DESC');

        if ($limit > 0) {
            $queryBuilder->setMaxResults($limit);
        }

        $events = $queryBuilder->getQuery()->getResult();

        return array_map(fn (Event $event): array => $this->mapEvent($event), $events);
    }

    public function countPublishedEvents(): int
    {
        return (int) $this->createPublishedEventQueryBuilder()
            ->select('COUNT(event.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countFoods(): int
    {
        return $this->entityManager->getRepository(Food::class)->count(['isEnabled' => true]);
    }

    public function countFoodCategories(): int
    {
        return $this->entityManager->getRepository(FoodCategory::class)->count(['isActive' => true]);
    }

    public function getFoods(): array
    {
        $foods = $this->entityManager
            ->getRepository(Food::class)
            ->createQueryBuilder('food')
            ->leftJoin('food.foodCategory', 'category')
            ->leftJoin('food.allergens', 'allergen')
            ->addSelect('category')
            ->addSelect('allergen')
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

        $foods = $this->createEnabledFoodQueryBuilder()
            ->andWhere('category.id IS NULL OR category.isActive = :categoryActive')
            ->setParameter('categoryActive', true)
            ->orderBy('food.isSpecial', 'DESC')
            ->addOrderBy('category.displayOrder', 'ASC')
            ->addOrderBy('food.name', 'ASC')
            ->getQuery()
            ->getResult();

        return $this->groupFoodsByCategory($categories, $foods);
    }

    private function mapCategory(DrinkCategory $category): array
    {
        $id = $category->getId() ?? 0;

        return [
            'slug' => 'cat-'.$id,
            'label' => $category->getName() ?? 'Categoria',
            'description' => $category->getDescription() ?? '',
        ];
    }

    private function mapDrink(Drink $drink): array
    {
        $category = $drink->getDrinkCategory();
        $categoryName = $category?->getName() ?? 'Senza categoria';
        $imagePath = $drink->getImageUrl();

        return [
            'name' => $drink->getName() ?? 'Drink',
            'description' => $drink->getDescription() ?? '',
            'price' => number_format((float) ($drink->getPrice() ?? 0), 2, '.', ''),
            'category' => strtolower($categoryName),
            'category_slug' => $category !== null ? 'cat-'.($category->getId() ?? 0) : 'uncategorized',
            'image' => $this->mediaStorage->resolvePublicUrl(
                Drink::class,
                $imagePath,
                'https://images.unsplash.com/photo-1514361892635-6f5b4d1cd4be?auto=format&fit=crop&w=900&q=80'
            ),
            'has_image' => $this->hasDisplayImage($imagePath),
            'beer_serving_type' => $drink->getBeerServingType(),
            'beer_serving_size' => $drink->getBeerServingSize(),
            'beer_small_price' => $drink->getBeerSmallPrice() !== null
                ? number_format((float) $drink->getBeerSmallPrice(), 2, '.', '')
                : null,
            'beer_medium_price' => $drink->getBeerMediumPrice() !== null
                ? number_format((float) $drink->getBeerMediumPrice(), 2, '.', '')
                : null,
            'is_special' => $drink->isSpecial(),
            'is_enabled' => $drink->isEnabled(),
        ];
    }

    private function mapFood(Food $food): array
    {
        $category = $food->getFoodCategory();
        $categoryLabel = $category?->getName() ?? 'Senza categoria';
        $imagePath = $food->getImageUrl();

        return [
            'name' => $food->getName() ?? 'Piatto',
            'description' => $food->getDescription() ?? '',
            'allergens' => $this->mapAllergens($food),
            'price' => number_format((float) ($food->getPrice() ?? 0), 2, '.', ''),
            'category' => strtolower($categoryLabel),
            'category_slug' => $category !== null ? 'food-cat-'.($category->getId() ?? 0) : 'food-uncategorized',
            'image' => $this->mediaStorage->resolvePublicUrl(
                Food::class,
                $imagePath,
                'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?auto=format&fit=crop&w=900&q=80'
            ),
            'has_image' => $this->hasDisplayImage($imagePath),
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
            'description' => $category->getDescription() ?? '',
        ];
    }

    private function mapEvent(Event $event): array
    {
        $startsAt = $event->getStartsAt();
        $endsAt = $event->getEndsAt();
        $ticketPrice = $event->getTicketPrice();
        $title = trim((string) ($event->getTitle() ?? ''));
        $description = trim((string) ($event->getDescription() ?? ''));
        $location = trim((string) ($event->getLocation() ?? ''));

        return [
            'title' => $title,
            'description' => $description !== '' ? $description : 'Musica, drink e atmosfera in riva al Po.',
            'location' => $location !== '' ? $location : 'Moloch, Borgoforte',
            'image' => $this->mediaStorage->resolvePublicUrl(
                Event::class,
                $event->getCoverImageUrl(),
                'https://images.unsplash.com/photo-1493225457124-a3eb161ffa5f?auto=format&fit=crop&w=1200&q=80'
            ),
            'starts_at_label' => $startsAt !== null ? $this->formatShortEventDate($startsAt) : '',
            'time_label' => $startsAt !== null ? $this->formatEventTimeRange($startsAt, $endsAt) : '',
            'date_range_label' => $startsAt !== null ? $this->formatEventDateRange($startsAt, $endsAt) : '',
            'ticket_label' => $ticketPrice !== null ? '€ '.number_format((float) $ticketPrice, 2, '.', '') : 'Ingresso libero',
            'is_free_entry' => $ticketPrice === null,
        ];
    }

    private function formatEventDateRange(\DateTimeImmutable $startsAt, ?\DateTimeImmutable $endsAt): string
    {
        if ($endsAt === null) {
            return $this->formatShortEventDate($startsAt);
        }

        if ($startsAt->format('Y-m-d') === $endsAt->format('Y-m-d')) {
            return $this->formatShortEventDate($startsAt);
        }

        return $this->formatShortEventDate($startsAt).' - '.$this->formatShortEventDate($endsAt);
    }

    private function formatShortEventDate(\DateTimeImmutable $date): string
    {
        return $date->format('d/m');
    }

    private function formatEventTimeRange(\DateTimeImmutable $startsAt, ?\DateTimeImmutable $endsAt): string
    {
        if ($endsAt === null || $startsAt->format('Y-m-d') !== $endsAt->format('Y-m-d')) {
            return $startsAt->format('H:i');
        }

        return $startsAt->format('H:i').' - '.$endsAt->format('H:i');
    }

    private function createPublishedEventQueryBuilder(): QueryBuilder
    {
        return $this->entityManager
            ->getRepository(Event::class)
            ->createQueryBuilder('event')
            ->andWhere('event.isPublished = :isPublished')
            ->setParameter('isPublished', true);
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
            ->leftJoin('food.allergens', 'allergen')
            ->addSelect('category')
            ->addSelect('allergen')
            ->andWhere('food.isEnabled = :isEnabled')
            ->setParameter('isEnabled', true);
    }

    /**
     * @param list<DrinkCategory> $categories
     * @param list<Drink> $drinks
     * @return list<array{category: array<string, string>, drinks: list<array<string, mixed>>}>
     */
    private function groupDrinksByCategory(array $categories, array $drinks): array
    {
        $groups = [];
        foreach ($categories as $category) {
            $categoryId = $category->getId();
            if ($categoryId === null) {
                continue;
            }

            $groups[$categoryId] = [
                'category' => $this->mapCategory($category),
                'drinks' => [],
            ];
        }

        foreach ($drinks as $drink) {
            $categoryId = $drink->getDrinkCategory()?->getId();
            if ($categoryId === null || !array_key_exists($categoryId, $groups)) {
                continue;
            }

            $groups[$categoryId]['drinks'][] = $this->mapDrink($drink);
        }

        return array_values($groups);
    }

    /**
     * @param list<DrinkCategory> $categories
     */
    private function sortDrinkCategoriesForFrontend(array &$categories): void
    {
        usort($categories, function (DrinkCategory $first, DrinkCategory $second): int {
            $firstPriority = $this->getImageCategoryPriority($first);
            $secondPriority = $this->getImageCategoryPriority($second);

            if ($firstPriority !== $secondPriority) {
                return $firstPriority <=> $secondPriority;
            }

            return [$first->getDisplayOrder() ?? 0, $first->getName() ?? '']
                <=> [$second->getDisplayOrder() ?? 0, $second->getName() ?? ''];
        });
    }

    private function getImageCategoryPriority(DrinkCategory $category): int
    {
        $label = strtolower(trim((string) $category->getName()));
        $priority = array_search($label, self::IMAGE_CATEGORY_LABELS, true);

        return $priority === false ? \PHP_INT_MAX : $priority;
    }

    private function hasDisplayImage(?string $imagePath): bool
    {
        return trim((string) $imagePath) !== '';
    }

    /**
     * @return list<array{label: string, icon: ?string}>
     */
    private function mapAllergens(Food $food): array
    {
        $mapped = [];

        foreach ($food->getAllergens() as $allergen) {
            $mapped[] = $this->mapAllergen($allergen);
        }

        usort(
            $mapped,
            static fn (array $first, array $second): int => $first['label'] <=> $second['label']
        );

        return $mapped;
    }

    /**
     * @return array{label: string, icon: ?string}
     */
    private function mapAllergen(Allergen $allergen): array
    {
        $label = $allergen->getName() ?? 'Allergene';
        $iconName = $this->resolveAllergenIconName($label);

        return [
            'label' => $label,
            'icon' => $iconName !== null ? '/images/allergens/'.$iconName.'.png' : null,
        ];
    }

    private function resolveAllergenIconName(string $label): ?string
    {
        return match ($this->normalizeAllergenKey($label)) {
            'glutine', 'gluten' => 'glutine',
            'soia', 'soy', 'soya' => 'soia',
            'latticini', 'latte', 'dairy', 'formaggio' => 'latticini',
            'arachidi', 'arachide', 'peanut', 'peanuts' => 'arachidi',
            'crostacei', 'molluschi', 'molloschi', 'crostacei-molluschi', 'crostacei-e-molluschi', 'shellfish' => 'crostacei-molluschi',
            default => null,
        };
    }

    private function normalizeAllergenKey(string $label): string
    {
        $asciiLabel = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $label);
        $normalizedLabel = strtolower(trim($asciiLabel !== false ? $asciiLabel : $label));

        return trim((string) preg_replace('/[^a-z0-9]+/', '-', $normalizedLabel), '-');
    }

    /**
     * @param list<FoodCategory> $categories
     * @param list<Food> $foods
     * @return list<array{category: array<string, string>, foods: list<array<string, mixed>>}>
     */
    private function groupFoodsByCategory(array $categories, array $foods): array
    {
        $groups = [];
        foreach ($categories as $category) {
            $categoryId = $category->getId();
            if ($categoryId === null) {
                continue;
            }

            $groups[$categoryId] = [
                'category' => $this->mapFoodCategory($category),
                'foods' => [],
            ];
        }

        foreach ($foods as $food) {
            $categoryId = $food->getFoodCategory()?->getId();
            if ($categoryId === null || !array_key_exists($categoryId, $groups)) {
                continue;
            }

            $groups[$categoryId]['foods'][] = $this->mapFood($food);
        }

        return array_values($groups);
    }
}
