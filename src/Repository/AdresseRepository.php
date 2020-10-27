<?php

namespace AcMarche\Bottin\Repository;

use AcMarche\Bottin\Entity\Adresse;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Adresse|null find($id, $lockMode = null, $lockVersion = null)
 * @method Adresse|null findOneBy(array $criteria, array $orderBy = null)
 * @method Adresse[]    findAll()
 * @method Adresse[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdresseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Adresse::class);
    }

    public function persist(Adresse $adresse)
    {
        $this->_em->persist($adresse);
    }

    public function remove(Adresse $adresse)
    {
        $this->_em->remove($adresse);
    }

    public function flush( )
    {
        $this->_em->flush();
    }
}
