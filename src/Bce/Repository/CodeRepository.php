<?php

namespace AcMarche\Bottin\Bce\Repository;

use AcMarche\Bottin\Bce\Entity\Code;
use AcMarche\Bottin\Doctrine\OrmCrudTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Code|null find($id, $lockMode = null, $lockVersion = null)
 * @method Code|null findOneBy(array $criteria, array $orderBy = null)
 * @method Code[]    findAll()
 * @method Code[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CodeRepository extends ServiceEntityRepository
{
    use OrmCrudTrait;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, Code::class);
    }

    public function checkExist(string $code, string $language, string $category): ?Code
    {
        return $this->createQueryBuilder('code')
            ->andWhere('code.code = :code')
            ->setParameter('code', $code)
            ->andWhere('code.language = :language')
            ->setParameter('language', $language)
            ->andWhere('code.category = :category')
            ->setParameter('category', $category)
            ->getQuery()
            ->getOneOrNullResult();
    }

}
