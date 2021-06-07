<?php

namespace AcMarche\Bottin\Repository;

use AcMarche\Bottin\Entity\Adresse;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Adresse|null find($id, $lockMode = null, $lockVersion = null)
 * @method Adresse|null findOneBy(array $criteria, array $orderBy = null)
 * @method Adresse[]    findAll()
 * @method Adresse[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdresseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, Adresse::class);
    }

    public function persist(Adresse $adresse): void
    {
        $this->_em->persist($adresse);
    }

    public function remove(Adresse $adresse): void
    {
        $this->_em->remove($adresse);
    }

    public function flush(): void
    {
        $this->_em->flush();
    }

    public function queryBuilderForSelect(): QueryBuilder
    {
        return $this->createQueryBuilder('adresse')->orderBy('adresse.nom');
    }
}
