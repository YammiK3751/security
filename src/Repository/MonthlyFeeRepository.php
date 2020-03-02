<?php

namespace App\Repository;

use App\Entity\MonthlyFee;
use App\Entity\User;
use App\Traits\RepositoryPaginatorTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;

/**
 * @method MonthlyFee|null find($id, $lockMode = null, $lockVersion = null)
 * @method MonthlyFee|null findOneBy(array $criteria, array $orderBy = null)
 * @method MonthlyFee[]    findAll()
 * @method MonthlyFee[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MonthlyFeeRepository extends ServiceEntityRepository
{
    use RepositoryPaginatorTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MonthlyFee::class);
    }

    /**
     * @param $filters
     * @param User $user
     * @param $orderBy
     * @param $order
     * @param int $currentPage
     * @param int $perPage
     * @return mixed
     */
    public function getAvailablePayments($filters, User $user, $orderBy, $order, $currentPage = 1, $perPage = 20)
    {
        $qb = $this->createQueryBuilder('mf');

        if (!$user->hasRole('ROLE_SUPER_ADMIN')) {
            $filters['user'] = $user->getId();
        }

        $qb = $this->applyFilters($qb, $filters);

        $qb->orderBy('mf.id', 'DESC');

        $paginator = $this->paginate($qb, $currentPage, $perPage);

        return $paginator;
    }

    protected function applyFilters(QueryBuilder $qb, $filters)
    {
        if (!empty($filters['user'])) {
            $qb
                ->leftJoin('mf.document', 'mfd')
                ->where($qb->expr()->eq('mfd.owner', ':user'))
                ->setParameter('user', $filters['user'])
            ;
        }

        return $qb;
    }

    // /**
    //  * @return MonthlyFee[] Returns an array of MonthlyFee objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?MonthlyFee
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
