<?php

namespace AcMarche\Bottin\Repository;

use AcMarche\Bottin\Entity\Situation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Situation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Situation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Situation[]    findAll()
 * @method Situation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SituationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Situation::class);
    }

    public function persist(Situation $situation)
    {
        $this->_em->persist($situation);
    }

    public function remove(Situation $situation)
    {
        $this->_em->remove($situation);
    }

    public function flush()
    {
        $this->_em->flush();
    }


}
