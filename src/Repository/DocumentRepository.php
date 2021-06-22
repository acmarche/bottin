<?php

namespace AcMarche\Bottin\Repository;

use AcMarche\Bottin\Entity\Document;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Document|null find($id, $lockMode = null, $lockVersion = null)
 * @method Document|null findOneBy(array $criteria, array $orderBy = null)
 * @method Document[]    findAll()
 * @method Document[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DocumentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, Document::class);
    }

    public function insert(Document $document): void
    {
        $this->persist($document);
        $this->flush();
    }

    public function persist(Document $document): void
    {
        $this->_em->persist($document);
    }

    public function remove(Document $document): void
    {
        $this->_em->remove($document);
    }

    public function flush(): void
    {
        $this->_em->flush();
    }
}
