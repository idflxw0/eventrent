<?php

namespace App\Repository;

use App\Entity\Maintenance;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class MaintenanceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Maintenance::class);
    }

    public function findByTechnician(int $userId): array
    {
        return $this->createQueryBuilder('m')
            ->join('m.equipment', 'e')->addSelect('e')
            ->where('m.technician = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('m.interventionDate', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
