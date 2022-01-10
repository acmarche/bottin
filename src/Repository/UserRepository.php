<?php

namespace AcMarche\Bottin\Repository;

use AcMarche\Bottin\Doctrine\OrmCrudTrait;
use AcMarche\Bottin\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Use|null  find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    use OrmCrudTrait;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, User::class);
    }

    public function findByEmailOrUsername(string $value): ?User
    {
        return $this->createQueryBuilder('user')
            ->andWhere('user.email = :value OR user.username = :value')
            ->setParameter('value', $value)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
