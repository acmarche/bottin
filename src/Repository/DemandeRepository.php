<?php

namespace AcMarche\Bottin\Repository;

use AcMarche\Bottin\Entity\Demande;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Demande|null find($id, $lockMode = null, $lockVersion = null)
 * @method Demande|null findOneBy(array $criteria, array $orderBy = null)
 * @method Demande[]    findAll()
 * @method Demande[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DemandeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, Demande::class);
    }

    /**
     * @return Demande[]
     */
    public function search(): array
    {
        $queryBuilder = $this->createQueryBuilder('demande');
        $queryBuilder->leftJoin('demande.fiche', 'fiche', 'WITH');
        $queryBuilder->leftJoin('demande.metas', 'metas', 'WITH');
        $queryBuilder->addSelect('fiche', 'metas');

        $queryBuilder->orderBy('demande.createdAt', 'DESC');

        $query = $queryBuilder->getQuery();

        return $query->getResult();
    }

    public function persist(Demande $demande): void
    {
        $this->_em->persist($demande);
    }

    public function flush(): void
    {
        $this->_em->flush();
    }

    public function remove(Demande $demande): void
    {
        $this->_em->remove($demande);
    }
}
