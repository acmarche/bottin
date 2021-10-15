<?php

namespace AcMarche\Bottin\Bce\Repository;

use AcMarche\Bottin\Bce\Entity\Address;
use AcMarche\Bottin\Doctrine\OrmCrudTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Address|null find($id, $lockMode = null, $lockVersion = null)
 * @method Address|null findOneBy(array $criteria, array $orderBy = null)
 * @method Address[]    findAll()
 * @method Address[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AddressRepository extends ServiceEntityRepository
{
    use OrmCrudTrait;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, Address::class);
    }

    public function checkExist(string $entityNumber, int $zipcode): ?Address
    {
        return $this->createQueryBuilder('address')
            ->andWhere('address.entityNumber = :entityNumber')
            ->setParameter('entityNumber', $entityNumber)
            ->andWhere('address.zipcode = :zipcode')
            ->setParameter('zipcode', $zipcode)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
