<?php

namespace AcMarche\Bottin\Repository;

use AcMarche\Bottin\Doctrine\OrmCrudTrait;
use AcMarche\Bottin\Entity\Fiche;
use AcMarche\Bottin\Entity\Localite;
use AcMarche\Bottin\Entity\Tag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Fiche|null find($id, $lockMode = null, $lockVersion = null)
 * @method Fiche|null findOneBy(array $criteria, array $orderBy = null)
 * @method Fiche[]    findAll()
 * @method Fiche[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FicheRepository extends ServiceEntityRepository
{
    use OrmCrudTrait;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, Fiche::class);
    }

    /**
     * @return Fiche[]
     */
    public function findByIds(array $ids): array
    {
        return $this->createQbl()
            ->andWhere('fiche IN (:ids)')
            ->setParameter('ids', $ids)
            ->getQuery()->getResult();
    }

    /**
     * @return Fiche[]
     */
    public function noLocation(): array
    {
        return $this->createQueryBuilder('fiche')
            ->andWhere('fiche.latitude IS NULL')
            ->getQuery()->getResult();
    }

    /**
     * @return Fiche[]
     */
    public function searchByNameAndCity(string $name, ?string $localite): array
    {
        $queryBuilder = $this->createQbl();

        if ('' !== $name) {
            $queryBuilder
                ->andWhere(
                    'fiche.societe LIKE :nom OR
                fiche.admin_email LIKE :nom OR
                fiche.email  LIKE :nom OR
                fiche.contact_email LIKE :nom OR
                fiche.societe LIKE :nom OR
                fiche.nom LIKE :nom OR
                fiche.prenom LIKE :nom'
                )
                ->setParameter('nom', '%'.$name.'%');
        }

        if ($localite) {
            $queryBuilder->andWhere(
                'fiche.localite = :localite OR (fiche.adresse IS NOT NULL AND adresse.localite = :localite) '
            )
                ->setParameter('localite', $localite);
        }

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @return Fiche[]
     */
    public function findAllWithJoins(): array
    {
        return $this->createQbl()
            ->addOrderBy('fiche.societe')
            ->getQuery()->getResult();
    }

    /**
     * @return Fiche[]
     */
    public function findByTag(Tag $tag): array
    {
        return $this->createQbl()
            ->andWhere(':tag2 MEMBER OF fiche.tags')
            ->setParameter('tag2', $tag)
            ->getQuery()->getResult();
    }

    /**
     * @return Fiche[]
     */
    public function findByLocalite(Localite $localite): array
    {
        return $this->createQbl()
            ->andWhere(
                'fiche.localite = :localite OR (fiche.adresse IS NOT NULL AND adresse.localite = :localite) '
            )
            ->setParameter('localite', $localite->nom)
            ->getQuery()->getResult();
    }

    private function createQbl(): QueryBuilder
    {
        return $this->createQueryBuilder('fiche')
            ->leftJoin('fiche.pdv', 'pdv', 'WITH')
            ->leftJoin('fiche.classements', 'classements', 'WITH')
            ->leftJoin('fiche.token', 'token', 'WITH')
            ->leftJoin('fiche.horaires', 'horaires', 'WITH')
            ->leftJoin('fiche.images', 'images', 'WITH')
            ->leftJoin('fiche.tags', 'tags', 'WITH')
            ->leftJoin('fiche.adresse', 'adresse', 'WITH')
            ->addSelect('pdv', 'classements', 'horaires', 'images', 'token', 'tags', 'adresse')
            ->addOrderBy('fiche.societe');
    }
}
