<?php

namespace AcMarche\Bottin\Repository;

use AcMarche\Bottin\Entity\Pdv;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Pdv|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pdv|null findOneBy(array $criteria, array $orderBy = null)
 *                                                                                                method Pdv[]    findAll()
 * @method Pdv[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PdvRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, Pdv::class);
    }

    /**
     * @return Pdv[]
     */
    public function findAll(): array
    {
        return $this->findBy([], ['intitule' => 'ASC']);
    }

    public function getForList(): \Doctrine\ORM\QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('p');
        $queryBuilder->orderBy('p.intitule');

        return $queryBuilder;
    }

    public function persist(Pdv $pdv): void
    {
        $this->_em->persist($pdv);
    }

    public function remove(Pdv $pdv): void
    {
        $this->_em->remove($pdv);
    }

    public function flush(): void
    {
        $this->_em->flush();
    }

}
