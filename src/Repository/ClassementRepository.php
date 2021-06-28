<?php

namespace AcMarche\Bottin\Repository;

use AcMarche\Bottin\Cap\Cap;
use AcMarche\Bottin\Entity\Category;
use AcMarche\Bottin\Entity\Classement;
use AcMarche\Bottin\Entity\Fiche;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Classement|null find($id, $lockMode = null, $lockVersion = null)
 * @method Classement|null findOneBy(array $criteria, array $orderBy = null)
 * @method Classement[]    findAll()
 * @method Classement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClassementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, Classement::class);
    }

    /**
     * @return Classement[]
     */
    public function getByFiche(Fiche $fiche, bool $onlyEco = false): array
    {
        $queryBuilder = $this->createQueryBuilder('c')
            ->leftJoin('c.fiche', 'f', 'WITH')
            ->leftJoin('c.category', 'cat', 'WITH')
            ->addSelect('f', 'cat')
            ->andWhere('c.fiche = :fiche')
            ->setParameter('fiche', $fiche)
            ->orderBy('c.principal', 'DESC');

        if ($onlyEco) {
            $queryBuilder->andWhere('cat.materializedPath LIKE :eco OR cat.materializedPath LIKE :sante')
                ->setParameter('eco', '%'.Cap::idEco.'%')
                ->setParameter('sante', '%'.Cap::idSante.'%');
        }

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @return Classement[]
     */
    public function findByCategories(array $categories): array
    {
        $queryBuilder = $this->createQueryBuilder('c')
            ->leftJoin('c.fiche', 'f', 'WITH')
            ->leftJoin('c.category', 'cat', 'WITH')
            ->addSelect('f', 'cat')
            ->andWhere('c.category IN (:categories)')
            ->setParameter('categories', $categories)
            ->orderBy('c.fiche');

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @throws NonUniqueResultException
     */
    public function checkExist(Fiche $fiche, Category $category): ?Classement
    {
        $queryBuilder = $this->createQueryBuilder('c')
            ->andWhere('c.category = :category')
            ->setParameter('category', $category)
            ->andWhere('c.fiche = :fiche')
            ->setParameter('fiche', $fiche)
            ->orderBy('c.fiche');

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }

    public function insert(Classement $classement): void
    {
        $this->_em->persist($classement);
        $this->flush();
    }

    public function persist(Classement $classement): void
    {
        $this->_em->persist($classement);
    }

    public function flush(): void
    {
        $this->_em->flush();
    }

    public function remove(Classement $classement): void
    {
        $this->_em->remove($classement);
    }
}
