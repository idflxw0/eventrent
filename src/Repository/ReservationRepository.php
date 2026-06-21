<?php

namespace App\Repository;

use App\Entity\Equipment;
use App\Entity\Reservation;
use App\Entity\ReservationLine;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservation::class);
    }

    public function findByUser(int $userId, ?string $status = null): array
    {
        $qb = $this->createQueryBuilder('r')
            ->leftJoin('r.lines', 'l')->addSelect('l')
            ->leftJoin('l.equipment', 'e')->addSelect('e')
            ->leftJoin('r.invoice', 'i')->addSelect('i')
            ->where('r.user = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('r.startDate', 'DESC');

        if ($status) {
            $qb->andWhere('r.status = :status')->setParameter('status', $status);
        }

        return $qb->getQuery()->getResult();
    }

    public function findOneWithRelations(int $id): ?Reservation
    {
        return $this->createQueryBuilder('r')
            ->leftJoin('r.lines', 'l')->addSelect('l')
            ->leftJoin('l.equipment', 'e')->addSelect('e')
            ->leftJoin('e.category', 'c')->addSelect('c')
            ->leftJoin('r.invoice', 'i')->addSelect('i')
            ->leftJoin('r.user', 'u')->addSelect('u')
            ->andWhere('r.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Returns equipment from $equipmentIds that are already booked during [start, end].
     */
    public function findConflictingEquipment(array $equipmentIds, \DateTimeImmutable $start, \DateTimeImmutable $end): array
    {
        if (empty($equipmentIds)) {
            return [];
        }

        return $this->getEntityManager()->createQueryBuilder()
            ->select('e')->distinct()
            ->from(Equipment::class, 'e')
            ->join(ReservationLine::class, 'rl', 'WITH', 'rl.equipment = e')
            ->join('rl.reservation', 'r')
            ->where('e.id IN (:ids)')
            ->andWhere('r.status NOT IN (:done)')
            ->andWhere('r.startDate < :end')
            ->andWhere('r.endDate > :start')
            ->setParameter('ids', $equipmentIds)
            ->setParameter('done', ['cancelled', 'completed'])
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->getQuery()
            ->getResult();
    }

    public function findAvailableEquipment(
        \DateTimeImmutable $start,
        \DateTimeImmutable $end,
        ?int $categoryId = null,
    ): array {
        $em = $this->getEntityManager();

        $qb = $em->createQueryBuilder();
        $qb->select('e')
            ->from(Equipment::class, 'e')
            ->leftJoin('e.category', 'c')->addSelect('c')
            ->where('e.availabilityStatus = :available')
            ->setParameter('available', Equipment::STATUS_AVAILABLE);

        if ($categoryId) {
            $qb->andWhere('c.id = :catId')
                ->setParameter('catId', $categoryId);
        }

        $sub = $em->createQueryBuilder();
        $sub->select('IDENTITY(rl.equipment)')
            ->from(ReservationLine::class, 'rl')
            ->join('rl.reservation', 'r')
            ->where('r.status != :cancelled')
            ->andWhere('r.startDate <= :endDate')
            ->andWhere('r.endDate >= :startDate');

        $qb->andWhere($qb->expr()->notIn('e.id', $sub->getDQL()))
            ->setParameter('cancelled', 'cancelled')
            ->setParameter('startDate', $start)
            ->setParameter('endDate', $end);

        return $qb->getQuery()->getResult();
    }
}
