<?php

namespace AcMarche\Bottin\Cbe\Repository;

use AcMarche\Bottin\Cbe\Entity\Branch;
use AcMarche\Bottin\Doctrine\OrmCrudTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Branch|null find($id, $lockMode = null, $lockVersion = null)
 * @method Branch|null findOneBy(array $criteria, array $orderBy = null)
 * @method Branch[]    findAll()
 * @method Branch[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BranchRepository extends ServiceEntityRepository
{
    use OrmCrudTrait;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, Branch::class);
    }

    public function checkExist(string $id): ?Branch
    {
        return $this->createQueryBuilder('branch')
            ->andWhere('branch.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

}
