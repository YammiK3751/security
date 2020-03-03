<?php

namespace App\Repository;

use App\Entity\Document;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use App\Traits\RepositoryPaginatorTrait;
use Doctrine\ORM\QueryBuilder;

/**
 * @method Document|null find($id, $lockMode = null, $lockVersion = null)
 * @method Document|null findOneBy(array $criteria, array $orderBy = null)
 * @method Document[]    findAll()
 * @method Document[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DocumentRepository extends ServiceEntityRepository
{
    use RepositoryPaginatorTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Document::class);
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
    public function getAvailableDocuments($filters, User $user, $orderBy, $order, $currentPage = 1, $perPage = 20)
    {
        $qb = $this->createQueryBuilder('d');

        if ($user->isUser()) {
            $filters['user'] = $user->getId();
        }

        $qb
            ->select('d')
            ->where(
                $qb->expr()->neq('d.status', ':status')
            )
            ->setParameter('status', Document::DOCUMENT_STATUS_CANCELLED)
        ;

        $qb = $this->applyFilters($qb, $filters);

        $qb->orderBy('d.id', 'DESC');

        $paginator = $this->paginate($qb, $currentPage, $perPage);

        return $paginator;
    }

    protected function applyFilters(QueryBuilder $qb, $filters)
    {
        if (!empty($filters['user'])) {
            $qb
                ->where($qb->expr()->eq('d.owner', ':user'))
                ->setParameter('user', $filters['user'])
            ;
        }

        return $qb;
    }
}
