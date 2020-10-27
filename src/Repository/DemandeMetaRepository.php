<?php

namespace AcMarche\Bottin\Repository;

use AcMarche\Bottin\Entity\DemandeMeta;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DemandeMeta|null find($id, $lockMode = null, $lockVersion = null)
 * @method DemandeMeta|null findOneBy(array $criteria, array $orderBy = null)
 * @method DemandeMeta[]    findAll()
 * @method DemandeMeta[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DemandeMetaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DemandeMeta::class);
    }

    public function persist(DemandeMeta $demandeMeta)
    {
        $this->_em->persist($demandeMeta);
    }

    public function flush()
    {
        $this->_em->flush();
    }
}
