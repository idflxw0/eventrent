<?php

namespace App\Repository;

use App\Entity\Quote;
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
}
