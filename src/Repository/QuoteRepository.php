<?php

namespace App\Repository;

use App\Entity\Equipment;
use App\Entity\Quote;
use App\Entity\QuoteLine;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class QuoteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Quote::class);
    }

    public function findByUser(int $userId, ?string $status = null): array
    {
        $qb = $this->createQueryBuilder('q')
            ->leftJoin('q.lines', 'l')->addSelect('l')
            ->leftJoin('l.equipment', 'e')->addSelect('e')
            ->where('q.user = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('q.createdAt', 'DESC');

        if ($status) {
            $qb->andWhere('q.status = :status')->setParameter('status', $status);
        }

        return $qb->getQuery()->getResult();
    }

    public function findOneWithRelations(int $id): ?Quote
    {
        return $this->createQueryBuilder('q')
            ->leftJoin('q.lines', 'l')->addSelect('l')
            ->leftJoin('l.equipment', 'e')->addSelect('e')
            ->leftJoin('e.category', 'c')->addSelect('c')
            ->leftJoin('q.user', 'u')->addSelect('u')
            ->andWhere('q.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findConflictingActiveQuotes(array $equipmentIds, \DateTimeImmutable $start, \DateTimeImmutable $end, int $userId): array
    {
        if (empty($equipmentIds)) {
            return [];
        }

        return $this->getEntityManager()->createQueryBuilder()
            ->select('e')->distinct()
            ->from(Equipment::class, 'e')
            ->join(QuoteLine::class, 'ql', 'WITH', 'ql.equipment = e')
            ->join('ql.quote', 'q')
            ->where('e.id IN (:ids)')
            ->andWhere('q.user = :user')
            ->andWhere('q.status IN (:active)')
            ->andWhere('q.requestedStartDate < :end')
            ->andWhere('q.requestedEndDate > :start')
            ->setParameter('ids', $equipmentIds)
            ->setParameter('user', $userId)
            ->setParameter('active', ['pending', 'approved'])
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->getQuery()
            ->getResult();
    }

    public function findPending(): array
    {
        return $this->createQueryBuilder('q')
            ->leftJoin('q.lines', 'l')->addSelect('l')
            ->leftJoin('l.equipment', 'e')->addSelect('e')
            ->leftJoin('q.user', 'u')->addSelect('u')
            ->where('q.status = :status')
            ->setParameter('status', 'pending')
            ->orderBy('q.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findExpired(): array
    {
        return $this->createQueryBuilder('q')
            ->where('q.status = :status')
            ->andWhere('q.createdAt < :limit')
            ->setParameter('status', 'pending')
            ->setParameter('limit', new \DateTimeImmutable('-15 days'))
            ->getQuery()
            ->getResult();
    }
}
