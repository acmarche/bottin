<?php

namespace AcMarche\Bottin\Repository;

use AcMarche\Bottin\Doctrine\OrmCrudTrait;
use AcMarche\Bottin\Entity\Localite;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Localite|null find($id, $lockMode = null, $lockVersion = null)
 * @method Localite|null findOneBy(array $criteria, array $orderBy = null)
 * @method Localite[]    findAll()
 * @method Localite[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LocaliteRepository extends ServiceEntityRepository
{
    use OrmCrudTrait;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, Localite::class);
    }

    /**
     * @return Localite[]
     */
    public function findAllOrderyByNom(): array
    {
        return $this->createQueryBuilder('localite')
            ->orderBy('localite.nom')
            ->getQuery()->getResult();
    }
}
