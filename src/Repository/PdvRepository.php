<?php

namespace AcMarche\Bottin\Repository;

use AcMarche\Bottin\Doctrine\OrmCrudTrait;
use AcMarche\Bottin\Entity\Pdv;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Pdv|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pdv|null findOneBy(array $criteria, array $orderBy = null)
 *                                                                                                method Pdv[]    findAll()
 * @method Pdv[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PdvRepository extends ServiceEntityRepository
{
    use OrmCrudTrait;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, Pdv::class);
    }

    /**
     * @return Pdv[]
     */
    public function findAll(): array
    {
        return $this->findBy([], ['intitule' => 'ASC']);
    }

    public function getForList(): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('p');
        $queryBuilder->orderBy('p.intitule');

        return $queryBuilder;
    }
}
