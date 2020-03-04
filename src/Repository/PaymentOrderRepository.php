<?php

namespace App\Repository;

use App\Entity\PaymentOrder;
use App\Entity\User;
use App\Traits\RepositoryPaginatorTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;

/**
 * @method PaymentOrder|null find($id, $lockMode = null, $lockVersion = null)
 * @method PaymentOrder|null findOneBy(array $criteria, array $orderBy = null)
 * @method PaymentOrder[]    findAll()
 * @method PaymentOrder[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PaymentOrderRepository extends ServiceEntityRepository
{
    use RepositoryPaginatorTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PaymentOrder::class);
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
    public function getAvailablePaymentOrders($filters, User $user, $orderBy, $order, $currentPage = 1, $perPage = 20)
    {
        $qb = $this->createQueryBuilder('po');

        if (!$user->hasRole('ROLE_SUPER_ADMIN')) {
            $filters['user'] = $user->getId();
        }

        $qb
            ->select('po')
            ->leftJoin('po.document', 'pod')
        ;

        $qb = $this->applyFilters($qb, $filters);

        $qb->orderBy('po.id', 'DESC');

        $paginator = $this->paginate($qb, $currentPage, $perPage);

        return $paginator;
    }

    protected function applyFilters(QueryBuilder $qb, $filters)
    {
        if (!empty($filters['user'])) {
            $qb
                ->where($qb->expr()->eq('pod.owner', ':user'))
                ->setParameter('user', $filters['user'])
            ;
        }

        if (!empty($filters['document'])) {
            $qb
                ->andWhere(
                    $qb->expr()->like('pod.code', ':document')
                )
                ->setParameter('document', '%' . $filters['document'] . '%')
            ;
        }

        if (!empty($filters['address'])) {
            $qb
                ->leftJoin('po.departure', 'dep')
                ->leftJoin('dep.apartment', 'ap')
                ->leftJoin('ap.house', 'h')
                ->leftJoin('h.street', 'st')
                ->leftJoin('h.region', 'reg')
                ->andWhere(
                    $qb->expr()->orX(
                        $qb->expr()->like('ap.number', ':address'),
                        $qb->expr()->like('h.number', ':address'),
                        $qb->expr()->like('st.name', ':address'),
                        $qb->expr()->like('reg.name', ':address')
                    )
                )
                ->setParameter('address', '%' . $filters['address'] . '%')
            ;
        }

        return $qb;
    }
}
