<?php

namespace App\Repository;

use App\Entity\Document;
use App\Entity\MonthlyFee;
use App\Entity\User;
use App\Traits\RepositoryPaginatorTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Validator\Constraints\DateTime;

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

        if ($user->isUser()) {
            $filters['user'] = $user->getId();
            $filters['currentDate'] = date('Y-m-d');
        }

        $qb
            ->select('mf')
            ->leftJoin('mf.document', 'mfd')
            ->andWhere(
                $qb->expr()->neq('mfd.status', ':status')
            )
            ->setParameter('status', Document::DOCUMENT_STATUS_CANCELLED)
        ;

        $qb = $this->applyFilters($qb, $filters);

        $qb->orderBy('mf.id', 'DESC');

        $paginator = $this->paginate($qb, $currentPage, $perPage);

        return $paginator;
    }

    protected function applyFilters(QueryBuilder $qb, $filters)
    {
        if (!empty($filters['user'])) {
            $qb
                ->andWhere(
                    $qb->expr()->eq('mfd.owner', ':user')
                )
                ->setParameter('user', $filters['user'])
            ;
        }

        if (!empty($filters['currentDate'])) {
            $qb
                ->andWhere('mf.paymentDate <= :currentDate')
                ->setParameter('currentDate', $filters['currentDate'])
            ;
        }

        if (!empty($filters['document'])) {
            $qb
                ->andWhere(
                    $qb->expr()->like('mfd.code', ':document')
                )
                ->setParameter('document', '%' . $filters['document'] . '%')
            ;
        }

        return $qb;
    }
}
