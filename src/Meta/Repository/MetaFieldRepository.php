<?php

namespace AcMarche\Bottin\Meta\Repository;

use AcMarche\Bottin\Doctrine\OrmCrudTrait;
use AcMarche\Bottin\Entity\MetaField;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MetaField|null find($id, $lockMode = null, $lockVersion = null)
 * @method MetaField|null findOneBy(array $criteria, array $orderBy = null)
 * @method MetaField[]    findAll()
 * @method MetaField[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MetaFieldRepository extends ServiceEntityRepository
{
    use OrmCrudTrait;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, MetaField::class);
    }
}
