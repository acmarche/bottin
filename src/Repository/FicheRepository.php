<?php

namespace AcMarche\Bottin\Repository;

use AcMarche\Bottin\Entity\Fiche;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Fiche|null find($id, $lockMode = null, $lockVersion = null)
 * @method Fiche|null findOneBy(array $criteria, array $orderBy = null)
 * @method Fiche[]    findAll()
 * @method Fiche[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FicheRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Fiche::class);
    }

    /**
     * @param array $ids
     * @return Fiche[]
     */
    public function findByIds(array $ids): array
    {
        return $this->createQueryBuilder('fiche')
            ->andWhere('fiche IN (:ids)')
            ->setParameter('ids', $ids)
            ->getQuery()->getResult();
    }

    /**
     * @return Fiche[]
     */
    public function noLocation()
    {
        $qb = $this->createQueryBuilder('f');

        $qb->andWhere('f.latitude IS NULL');

        return $qb->getQuery()->getResult();
    }

    /**
     * @param $args
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function setCriteria($args)
    {
        $nom = isset($args['nom']) ? $args['nom'] : '';
        $societe = isset($args['societe']) ? $args['societe'] : '';
        $localite = isset($args['localite']) ? $args['localite'] : null;
        $categories = isset($args['categories']) ? $args['categories'] : null;
        $horaire = isset($args['horaire']) ? $args['horaire'] : null;
        $fiche = isset($args['fiche']) ? $args['fiche'] : null;

        $qb = $this->createQueryBuilder('f');
        $qb->leftJoin('f.pdv', 'pdv', 'WITH');
        $qb->leftJoin('f.classements', 'classements', 'WITH');
        $qb->leftJoin('f.horaires', 'horaires', 'WITH');
        $qb->leftJoin('f.images', 'images', 'WITH');
        $qb->addSelect('pdv', 'classements', 'horaires', 'images');

        if ($societe) {
            $qb->andWhere('f.societe LIKE :societe')
                ->setParameter('societe', '%'.$societe.'%');
        }

        if ($nom) {
            $qb->andWhere(
                'f.admin_email LIKE :nom OR 
                f.email  LIKE :nom OR 
                f.contact_email LIKE :nom OR 
                f.societe LIKE :nom OR
                f.nom LIKE :nom OR 
                f.prenom LIKE :nom'
            )->setParameter('nom', '%'.$nom.'%');
        }

        if ($categories) {
            $categories = is_array($categories) ? $categories : [$categories];
            $categories = implode(',', $categories);
            $qb->andWhere("classements.category IN ($categories)");
        }

        if ($horaire) {
            $qb->andWhere('horaires IS NULL');
        }

        if ($fiche) {
            $qb->andWhere('f = :fiche')
                ->setParameter('fiche', $fiche);
        }

        $qb->orderBy('f.societe');

        return $qb;
    }

    /**
     * @param $args
     *
     * @return Fiche|Fiche[]
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function search($args)
    {
        $qb = $this->setCriteria($args);

        return $qb->getQuery()->getResult();
    }

    public function insert(Fiche $fiche)
    {
        $this->persist($fiche);
        $this->flush();
    }

    public function persist(Fiche $fiche)
    {
        $this->_em->persist($fiche);
    }

    public function flush()
    {
        $this->_em->flush();
    }

    public function remove(Fiche $fiche)
    {
        $this->_em->remove($fiche);
    }


}
