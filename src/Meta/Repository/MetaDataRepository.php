<?php

namespace AcMarche\Bottin\Meta\Repository;

use AcMarche\Bottin\Doctrine\OrmCrudTrait;
use AcMarche\Bottin\Entity\Fiche;
use AcMarche\Bottin\Entity\MetaData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MetaData|null find($id, $lockMode = null, $lockVersion = null)
 * @method MetaData|null findOneBy(array $criteria, array $orderBy = null)
 * @method MetaData[]    findAll()
 * @method MetaData[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MetaDataRepository extends ServiceEntityRepository
{
    use OrmCrudTrait;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, MetaData::class);
    }

    public function findOneByFicheAndName(Fiche $fiche, string $name): ?MetaData
    {
        return $this->createQueryBuilder('meta_data')
            ->andWhere('meta_data.fiche = :fiche')
            ->setParameter('fiche', $fiche)
            ->andWhere('meta_data.name = :name')
            ->setParameter('name', $name)
            ->getQuery()
            ->getOneOrNullResult();

    }

    public function findOneByName( string $name): ?MetaData
    {
        return $this->createQueryBuilder('meta_data')
            ->andWhere('meta_data.fieldName = :name')
            ->setParameter('name', $name)
            ->getQuery()
            ->getOneOrNullResult();

    }
}
