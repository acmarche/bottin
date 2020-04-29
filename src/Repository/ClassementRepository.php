<?php

namespace AcMarche\Bottin\Repository;

use AcMarche\Bottin\Cap\Cap;
use AcMarche\Bottin\Entity\Classement;
use AcMarche\Bottin\Entity\Fiche;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Classement|null find($id, $lockMode = null, $lockVersion = null)
 * @method Classement|null findOneBy(array $criteria, array $orderBy = null)
 * @method Classement[]    findAll()
 * @method Classement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClassementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Classement::class);
    }

    /**
     * @param Fiche $fiche
     * @param bool $onlyEco
     * @return Classement[]
     */
    public function getByFiche(Fiche $fiche, bool $onlyEco = false)
    {
        $qb = $this->createQueryBuilder('c')
            ->leftJoin('c.fiche', 'f', 'WITH')
            ->leftJoin('c.category', 'cat', 'WITH')
            ->addSelect('f', 'cat')
            ->andWhere('c.fiche = :fiche')
            ->setParameter('fiche', $fiche)
            ->orderBy('c.principal', 'DESC');

        if ($onlyEco) {
            $qb->andWhere('cat.materializedPath LIKE :eco OR cat.materializedPath LIKE :sante')
                ->setParameter('eco', '%'.Cap::idEco.'%')
                ->setParameter('sante', '%'.Cap::idSante.'%');
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @param array $categories
     * @return Classement[]
     */
    public function findByCategories(array $categories)
    {
        $qb = $this->createQueryBuilder('c')
            ->leftJoin('c.fiche', 'f', 'WITH')
            ->leftJoin('c.category', 'cat', 'WITH')
            ->addSelect('f', 'cat')
            ->andWhere('c.category IN (:categories)')
            ->setParameter('categories', $categories)
            ->orderBy('c.fiche');

        return $qb->getQuery()->getResult();
    }

    public function insert(Classement $classement)
    {
        $this->_em->persist($classement);
        $this->flush();
    }

    public function persist(Classement $classement)
    {
        $this->_em->persist($classement);
    }

    public function flush()
    {
        $this->_em->flush();
    }

    public function remove(Classement $classement)
    {
        $this->_em->remove($classement);
    }
}
