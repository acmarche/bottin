<?php

namespace AcMarche\Bottin\Repository;

use AcMarche\Bottin\Doctrine\OrmCrudTrait;
use AcMarche\Bottin\Entity\Fiche;
use AcMarche\Bottin\Entity\Token;
use DateTimeInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Token|null find($id, $lockMode = null, $lockVersion = null)
 * @method Token|null findOneBy(array $criteria, array $orderBy = null)
 * @method Token[]    findAll()
 * @method Token[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TokenRepository extends ServiceEntityRepository
{
    use OrmCrudTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Token::class);
    }

    public function findByFiche(Fiche $fiche): ?Token
    {
        return $this->createQueryBuilder('token')
            ->andWhere('token.fiche = :val')
            ->setParameter('val', $fiche)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function isValid(Fiche $fiche, DateTimeInterface $date): ?Token
    {
        return $this->createQueryBuilder('token')
            ->andWhere('token.fiche = :fiche')
            ->setParameter('fiche', $fiche)
            ->andWhere('token.created_at >= :val')
            ->setParameter('val', $date->format('Y-m-d'))
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findOneByUuid(string $tokenParam): ?Token
    {
        return $this->createQueryBuilder('token')
            ->andWhere('token.uuid = :val')
            ->setParameter('val', $tokenParam)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findOneByPassword(string $password): ?Token
    {
        return $this->createQueryBuilder('token')
            ->andWhere('token.password = :val')
            ->setParameter('val', $password)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
