<?php

namespace AcMarche\Bottin\Bce\Repository;

use AcMarche\Bottin\Bce\Entity\Contact;
use AcMarche\Bottin\Doctrine\OrmCrudTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Contact|null find($id, $lockMode = null, $lockVersion = null)
 * @method Contact|null findOneBy(array $criteria, array $orderBy = null)
 * @method Contact[]    findAll()
 * @method Contact[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContactRepository extends ServiceEntityRepository
{
    use OrmCrudTrait;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, Contact::class);
    }

    public function checkExist(string $entityContact, string $entityNumber, string $contactType): ?Contact
    {
        return $this->createQueryBuilder('contact')
            ->andWhere('contact.entityContact = :entityContact')
            ->setParameter('entityContact', $entityContact)
            ->andWhere('contact.entityNumber = :entityNumber')
            ->setParameter('entityNumber', $entityNumber)
            ->andWhere('contact.contactType = :contactType')
            ->setParameter('contactType', $contactType)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
