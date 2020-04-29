<?php

namespace AcMarche\Bottin\Repository;

use AcMarche\Bottin\Entity\FicheImage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method FicheImage|null find($id, $lockMode = null, $lockVersion = null)
 * @method FicheImage|null findOneBy(array $criteria, array $orderBy = null)
 * @method FicheImage[]    findAll()
 * @method FicheImage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FicheImage::class);
    }

    public function insert(FicheImage $ficheImage)
    {
        $this->_em->persist($ficheImage);
        $this->save();
    }

    public function persist(FicheImage $ficheImage)
    {
        $this->_em->persist($ficheImage);
    }

    public function flush()
    {
        $this->_em->flush();
    }

    public function remove(FicheImage $ficheImage)
    {
        $this->_em->remove($ficheImage);
    }
}
