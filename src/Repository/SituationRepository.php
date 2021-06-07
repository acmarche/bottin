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
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, Situation::class);
    }

    public function persist(Situation $situation): void
    {
        $this->_em->persist($situation);
    }

    public function remove(Situation $situation): void
    {
        $this->_em->remove($situation);
    }

    public function flush(): void
    {
        $this->_em->flush();
    }


}
