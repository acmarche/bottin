<?php

namespace AcMarche\Bottin\Entity\Traits;

use AcMarche\Bottin\Entity\Document;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

trait DocumentsTrait
{
    /**
     * @var Document[]|Collection|iterable
     * @ORM\OneToMany(targetEntity="AcMarche\Bottin\Entity\Document", mappedBy="fiche", cascade={"persist", "remove"})
     */
    private iterable $documents;

    /**
     * @return Collection|Document[]
     */
    public function getDocuments(): Collection
    {
        return $this->documents;
    }

    public function addDocument(Document $document): self
    {
        if (!$this->documents->contains($document)) {
            $this->documents[] = $document;
            $document->setFiche($this);
        }

        return $this;
    }

    public function removeDocument(Document $document): self
    {
        if ($this->documents->contains($document)) {
            $this->documents->removeElement($document);
            // set the owning side to null (unless already changed)
            if ($document->getFiche() === $this) {
                $document->setFiche(null);
            }
        }

        return $this;
    }

    /**
     * Pour elastic.
     */
    public function setDocuments(array $documents)
    {
        $this->documents = $documents;
    }
}
