<?php

namespace AcMarche\Bottin\Tag\Repository;

use AcMarche\Bottin\Doctrine\OrmCrudTrait;
use AcMarche\Bottin\Entity\Tag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Tag|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tag|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tag[]    findAll()
 * @method Tag[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TagRepository extends ServiceEntityRepository
{
    use OrmCrudTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tag::class);
    }

    /**
     * @return Tag[]
     */
    public function search(iterable $args): array
    {
        $nom = $args['name'] ?? null;

        $qb = $this->createQb();

        return $qb->getQuery()->getResult();
    }

    /**
     * @return Tag[]
     */
    public function findAllOrdered(): array
    {
        return $this->createQb()->getQuery()->getResult();
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findOneByName(string $name): ?Tag
    {
        return $this->createQb()
            ->andWhere('tag.name = :name')
            ->setParameter('name', $name)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param string $name
     * @return Tag[]
     */
    public function findByName(string $name): QueryBuilder
    {
        return $this->createQb()
            ->andWhere('tag.name LIKE :name')
            ->setParameter('name', '%'.$name.'%');
    }

    public function createQb(): QueryBuilder
    {
        return $this->createQueryBuilder('tag')
            ->addOrderBy('tag.name', 'ASC');
    }
}
