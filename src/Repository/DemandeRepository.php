<?php

namespace AcMarche\Bottin\Repository;

use AcMarche\Bottin\Entity\Demande;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Demande|null find($id, $lockMode = null, $lockVersion = null)
 * @method Demande|null findOneBy(array $criteria, array $orderBy = null)
 * @method Demande[]    findAll()
 * @method Demande[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DemandeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Demande::class);
    }

    /**
     * @return Demande[]
     */
    public function search()
    {
        $qb = $this->createQueryBuilder('demande');
        $qb->leftJoin('demande.fiche', 'fiche', 'WITH');
        $qb->leftJoin('demande.metas', 'metas', 'WITH');
        $qb->addSelect('fiche', 'metas');

        $qb->orderBy('demande.createdAt', 'DESC');

        $query = $qb->getQuery();

        return $query->getResult();
    }

    public function persist(Demande $demande)
    {
        $this->_em->persist($demande);
    }

    public function flush()
    {
        $this->_em->flush();
    }

    public function remove(Demande $demande)
    {
        $this->_em->remove($demande);
    }
}
