<?php

namespace AcMarche\Bottin\Cbe\Repository;

use AcMarche\Bottin\Cbe\Entity\Entreprise;
use AcMarche\Bottin\Doctrine\OrmCrudTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Entreprise|null find($id, $lockMode = null, $lockVersion = null)
 * @method Entreprise|null findOneBy(array $criteria, array $orderBy = null)
 * @method Entreprise[]    findAll()
 * @method Entreprise[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EntrepriseRepository extends ServiceEntityRepository
{
    use OrmCrudTrait;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, Entreprise::class);
    }

    public function checkExist(string $entreprise, string $language, string $category): ?Entreprise
    {
        return $this->createQueryBuilder('entreprise')
            ->andWhere('entreprise.entreprise = :entreprise')
            ->setParameter('entreprise', $entreprise)
            ->andWhere('entreprise.language = :language')
            ->setParameter('language', $language)
            ->andWhere('entreprise.category = :category')
            ->setParameter('category', $category)
            ->getQuery()
            ->getOneOrNullResult();
    }

}
