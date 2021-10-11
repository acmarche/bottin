<?php

namespace AcMarche\Bottin\Bce\Repository;

use AcMarche\Bottin\Bce\Entity\Addresse;
use AcMarche\Bottin\Doctrine\OrmCrudTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Addresse|null find($id, $lockMode = null, $lockVersion = null)
 * @method Addresse|null findOneBy(array $criteria, array $orderBy = null)
 * @method Addresse[]    findAll()
 * @method Addresse[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AddresseRepository extends ServiceEntityRepository
{
    use OrmCrudTrait;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, Addresse::class);
    }

    public function checkExist(string $entityNumber, int $zipcode): ?Addresse
    {
        return $this->createQueryBuilder('entityNumber')
            ->andWhere('addresse.entityNumber = :entityNumber')
            ->setParameter('entityNumber', $entityNumber)
            ->andWhere('addresse.zipcode = :zipcode')
            ->setParameter('zipcode', $zipcode)
            ->getQuery()
            ->getOneOrNullResult();
    }

}
