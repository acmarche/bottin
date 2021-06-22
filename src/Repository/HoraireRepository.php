<?php

namespace AcMarche\Bottin\Repository;

use AcMarche\Bottin\Entity\Horaire;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Horaire|null find($id, $lockMode = null, $lockVersion = null)
 * @method Horaire|null findOneBy(array $criteria, array $orderBy = null)
 * @method Horaire[]    findAll()
 * @method Horaire[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HoraireRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, Horaire::class);
    }

    public function insert(Horaire $horaire): void
    {
        $this->persist($horaire);
        $this->flush();
    }

    public function persist(Horaire $horaire): void
    {
        $this->_em->persist($horaire);
    }

    public function flush(): void
    {
        $this->_em->flush();
    }
}
