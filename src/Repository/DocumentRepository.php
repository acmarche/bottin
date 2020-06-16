<?php

namespace AcMarche\Bottin\Repository;

use AcMarche\Bottin\Entity\Document;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Document|null find($id, $lockMode = null, $lockVersion = null)
 * @method Document|null findOneBy(array $criteria, array $orderBy = null)
 * @method Document[]    findAll()
 * @method Document[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DocumentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Document::class);
    }

    public function insert(Document $document)
    {
        $this->persist($document);
        $this->flush();
    }

    public function persist(Document $document)
    {
        $this->_em->persist($document);
    }

    public function remove(Document $document)
    {
        $this->_em->remove($document);
    }

    public function flush()
    {
        $this->_em->flush();
    }


}
