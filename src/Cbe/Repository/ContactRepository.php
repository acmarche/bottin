<?php

namespace AcMarche\Bottin\Cbe\Repository;

use AcMarche\Bottin\Cbe\Entity\Contact;
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

    public function checkExist(string $contact, string $language, string $category): ?Contact
    {
        return $this->createQueryBuilder('contact')
            ->andWhere('contact.contact = :contact')
            ->setParameter('contact', $contact)
            ->andWhere('contact.language = :language')
            ->setParameter('language', $language)
            ->andWhere('contact.category = :category')
            ->setParameter('category', $category)
            ->getQuery()
            ->getOneOrNullResult();
    }

}
