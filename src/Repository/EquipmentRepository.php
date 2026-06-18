<?php

namespace App\Repository;

use App\Entity\Equipment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Equipment>
 */
class EquipmentRepository extends ServiceEntityRepository
{
    public const SORT_NAME      = 'name';
    public const SORT_NAME_DESC = 'name_desc';
    public const SORT_PRICE     = 'price';
    public const SORT_PRICE_DESC = 'price_desc';
    public const SORT_NEWEST    = 'newest';
    public const DEFAULT_LIMIT  = 9;

    private const SORT_MAP = [
        self::SORT_NAME      => 'e.name ASC',
        self::SORT_NAME_DESC => 'e.name DESC',
        self::SORT_PRICE     => 'e.dailyPrice ASC',
        self::SORT_PRICE_DESC=> 'e.dailyPrice DESC',
        self::SORT_NEWEST    => 'e.addedAt DESC',
    ];

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Equipment::class);
    }

    public function search(
        ?int $categoryId = null,
        ?string $availability = null,
        ?float $priceMax = null,
        string $sort = self::SORT_NAME,
        int $page = 1,
        int $limit = self::DEFAULT_LIMIT,
    ): Paginator {
        $qb = $this->createQueryBuilder('e')
            ->leftJoin('e.category', 'c')->addSelect('c')
            ->leftJoin('e.supplier', 's')->addSelect('s')
            ->leftJoin('e.reviews', 'r')->addSelect('r');

        if ($categoryId !== null) {
            $qb->andWhere('c.id = :categoryId')
                ->setParameter('categoryId', $categoryId);
        }

        if ($availability === Equipment::STATUS_AVAILABLE) {
            $qb->andWhere('e.availabilityStatus = :status')
                ->setParameter('status', Equipment::STATUS_AVAILABLE);
        }

        if ($priceMax !== null && $priceMax > 0) {
            $qb->andWhere('e.dailyPrice <= :priceMax')
                ->setParameter('priceMax', (string) $priceMax);
        }

        $orderBy = self::SORT_MAP[$sort] ?? self::SORT_MAP[self::SORT_NAME];
        $qb->orderBy(...explode(' ', $orderBy));

        $qb->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);

        return new Paginator($qb, fetchJoinCollection: true);
    }

    public function findOneWithRelations(int $id): ?Equipment
    {
        return $this->createQueryBuilder('e')
            ->leftJoin('e.category', 'c')->addSelect('c')
            ->leftJoin('e.supplier', 's')->addSelect('s')
            ->leftJoin('e.reviews', 'r')->addSelect('r')
            ->leftJoin('r.user', 'u')->addSelect('u')
            ->leftJoin('e.accessories', 'a')->addSelect('a')
            ->andWhere('e.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function countByCategory(): array
    {
        return $this->createQueryBuilder('e')
            ->select('c.name as category_name, c.id as category_id, COUNT(e.id) as cnt')
            ->leftJoin('e.category', 'c')
            ->groupBy('c.id, c.name')
            ->orderBy('c.name')
            ->getQuery()
            ->getArrayResult();
    }
}
