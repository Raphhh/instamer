<?php

namespace App\Repository;

use App\Entity\Account;
use App\Utils\GeneratorQueryTransformerTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\AbstractQuery;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Account|null find($id, $lockMode = null, $lockVersion = null)
 * @method Account|null findOneBy(array $criteria, array $orderBy = null)
 * @method Account[]    findAll()
 * @method Account[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AccountRepository extends ServiceEntityRepository
{
    use GeneratorQueryTransformerTrait;

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Account::class);
    }

    /**
     * @param int $hydrationMode
     * @return \Generator|Account[]
     */
    public function generateAll($hydrationMode = AbstractQuery::HYDRATE_OBJECT)
    {
        return $this->toGenerator(
            $this->createQueryBuilder('account')
                ->orderBy('account.creationDatetime', 'ASC')
                ->getQuery(),
            null,
            $hydrationMode
        );
    }
}
