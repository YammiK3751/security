<?php

namespace App\Repository;

use App\Entity\SecurityApartment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method SecurityApartment|null find($id, $lockMode = null, $lockVersion = null)
 * @method SecurityApartment|null findOneBy(array $criteria, array $orderBy = null)
 * @method SecurityApartment[]    findAll()
 * @method SecurityApartment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SecurityApartmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SecurityApartment::class);
    }

    // /**
    //  * @return SecurityApartment[] Returns an array of SecurityApartment objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?SecurityApartment
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
