<?php

namespace App\Repository;

use App\Entity\Following;
use App\Utils\GeneratorQueryTransformerTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\AbstractQuery;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Following|null find($id, $lockMode = null, $lockVersion = null)
 * @method Following|null findOneBy(array $criteria, array $orderBy = null)
 * @method Following[]    findAll()
 * @method Following[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FollowingRepository extends ServiceEntityRepository
{
    use GeneratorQueryTransformerTrait;

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Following::class);
    }

    /**
     * @param array $ids
     * @param int $hydrationMode
     * @return \Generator|Following[]
     */
    public function generateActivesBut(array $ids, $hydrationMode = AbstractQuery::HYDRATE_OBJECT)
    {
        return $this->toGenerator(
            $this->createQueryBuilder('account')
                ->andWhere('account.id NOT IN(:ids)')
                ->andWhere('account.deletionDatetime IS NULL')
                ->orderBy('account.creationDatetime', 'ASC')
                ->setParameter('ids', $ids)
                ->getQuery(),
            null,
            $hydrationMode
        );
    }

    /**
     * @param array $ids
     * @param int $hydrationMode
     * @return \Generator|Following[]
     */
    public function generateReciprocalsBut(array $ids, $hydrationMode = AbstractQuery::HYDRATE_OBJECT)
    {
        return $this->toGenerator(
            $this->createQueryBuilder('account')
                ->andWhere('account.id NOT IN(:ids)')
                ->andWhere('account.isReciprocal = 1')
                ->orderBy('account.creationDatetime', 'ASC')
                ->setParameter('ids', $ids)
                ->getQuery(),
            null,
            $hydrationMode
        );
    }
}
