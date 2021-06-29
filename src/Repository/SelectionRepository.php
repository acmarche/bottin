<?php

namespace AcMarche\Bottin\Repository;

use AcMarche\Bottin\Entity\Selection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Selection|null find($id, $lockMode = null, $lockVersion = null)
 * @method Selection|null findOneBy(array $criteria, array $orderBy = null)
 * @method Selection[]    findAll()
 * @method Selection[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SelectionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Selection::class);
    }

    /**
     * @return Selection[] Returns an array of Selection objects
     */
    public function findByUser(string $username): array
    {
        return $this->createQueryBuilder('selection')
            ->andWhere('selection.user = :user')
            ->setParameter('user', $username)
            ->getQuery()
            ->getResult();
    }

    public function persist(Selection $selection): void
    {
        $this->_em->persist($selection);
    }

    public function remove(Selection $selection): void
    {
        $this->_em->remove($selection);
    }

    public function flush(): void
    {
        $this->_em->flush();
    }

}
