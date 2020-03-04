<?php

namespace App\Repository;

use App\Entity\Apartment;
use App\Entity\Document;
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

    /**
     * @return mixed
     */
    public function getAvailableApartments()
    {
        $qb = $this->createQueryBuilder('sa');

        $qb
            ->select('ap')
            ->from('App:Apartment', 'ap')
            ->leftJoin('sa.document', 'd')
            ->where(
                $qb->expr()->eq('d.status', ':status')
            )
            ->setParameter('status', Document::DOCUMENT_STATUS_APPROVED)
        ;

        return $qb->getQuery()->getResult();
    }
}
