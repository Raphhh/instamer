<?php

namespace App\Repository;

use App\Entity\Account;
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
     * @param Account $account
     * @param array $ids
     * @param int $hydrationMode
     * @return \Generator|Following[]
     */
    public function generateActivesBut(Account $account, array $ids, $hydrationMode = AbstractQuery::HYDRATE_OBJECT)
    {
        return $this->toGenerator(
            $this->createQueryBuilder('f')
                ->andWhere('f.account = :account')
                ->andWhere('f.id NOT IN(:ids)')
                ->andWhere('f.deletionDatetime IS NULL')
                ->orderBy('f.creationDatetime', 'ASC')
                ->setParameter('account', $account)
                ->setParameter('ids', $ids)
                ->getQuery(),
            null,
            $hydrationMode
        );
    }

    /**
     * @param Account $account
     * @param array $ids
     * @param int $hydrationMode
     * @return \Generator|Following[]
     */
    public function generateReciprocalsBut(Account $account, array $ids, $hydrationMode = AbstractQuery::HYDRATE_OBJECT)
    {
        return $this->toGenerator(
            $this->createQueryBuilder('f')
                ->andWhere('f.account = :account')
                ->andWhere('f.id NOT IN(:ids)')
                ->andWhere('f.isReciprocal = 1')
                ->orderBy('f.creationDatetime', 'ASC')
                ->setParameter('account', $account)
                ->setParameter('ids', $ids)
                ->getQuery(),
            null,
            $hydrationMode
        );
    }

    /**
     * @param Account $account
     * @param \DateTime $before
     * @param $isReciprocal
     * @param int $hydrationMode
     * @return \Generator|Following[]
     */
    public function generateDeactivables(Account $account, \DateTime $before, $isReciprocal, $hydrationMode = AbstractQuery::HYDRATE_OBJECT)
    {
        return $this->toGenerator(
            $this->createQueryBuilder('f')
                ->andWhere('f.account = :account')
                ->andWhere('f.deletionDatetime IS NULL')
                ->andWhere('f.isFrozen = 0')
                ->andWhere('f.isReciprocal = :is_reciprocal')
                ->andWhere('f.creationDatetime < :before')
                ->orderBy('f.creationDatetime', 'ASC')
                ->setParameter('account', $account)
                ->setParameter('is_reciprocal', $isReciprocal)
                ->setParameter('before', $before)
                ->getQuery(),
            null,
            $hydrationMode
        );
    }
}
