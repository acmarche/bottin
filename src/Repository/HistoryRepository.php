<?php

namespace AcMarche\Bottin\Repository;

use AcMarche\Bottin\Doctrine\OrmCrudTrait;
use AcMarche\Bottin\Entity\Fiche;
use AcMarche\Bottin\Entity\History;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method History|null find($id, $lockMode = null, $lockVersion = null)
 * @method History|null findOneBy(array $criteria, array $orderBy = null)
 * @method History[]    findAll()
 * @method History[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HistoryRepository extends ServiceEntityRepository
{
    use OrmCrudTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, History::class);
    }

    /**
     * @return History[] Returns an array of History objects
     */
    public function findByFiche(Fiche $fiche): array
    {
        return $this->createQueryBuilder('h')
            ->leftJoin('h.fiche', 'fiche', 'WITH')
            ->addSelect('fiche')
            ->andWhere('h.fiche = :fiche')
            ->setParameter('fiche', $fiche)
            ->orderBy('h.createdAt', 'DESC')
            ->setMaxResults(100)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return History[] Returns an array of History objects
     */
    public function findOrdered(): array
    {
        return $this->createQueryBuilder('h')
            ->leftJoin('h.fiche', 'fiche', 'WITH')
            ->addSelect('fiche')
            ->orderBy('h.createdAt', 'DESC')
            ->setMaxResults(200)
          //  ->groupBy('h.fiche')
            ->getQuery()
            ->getResult();
    }
}
