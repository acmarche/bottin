<?php

namespace AcMarche\Bottin\Repository;

use AcMarche\Bottin\Entity\Pdv;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Pdv|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pdv|null findOneBy(array $criteria, array $orderBy = null)
 *                                                                                                method Pdv[]    findAll()
 * @method Pdv[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PdvRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pdv::class);
    }

    /**
     * @return Pdv[]
     */
    public function findAll()
    {
        return $this->findBy([], ['intitule' => 'ASC']);
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getForList()
    {
        $qb = $this->createQueryBuilder('p');
        $qb->orderBy('p.intitule');

        return $qb;
    }

    public function persist(Pdv $pdv)
    {
        $this->_em->persist($pdv);
    }

    public function remove(Pdv $pdv)
    {
        $this->_em->remove($pdv);
    }

    public function flush()
    {
        $this->_em->flush();
    }

}
