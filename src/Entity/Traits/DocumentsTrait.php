<?php

namespace AcMarche\Bottin\Entity\Traits;

use AcMarche\Bottin\Entity\Document;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

trait DocumentsTrait
{
    /**
     * @var Document[]|Collection|iterable
     */
    #[ORM\OneToMany(targetEntity: Document::class, mappedBy: 'fiche', cascade: ['persist', 'remove'])]
    public iterable $documents;

    public function addDocument(Document $document): self
    {
        if (!$this->documents->contains($document)) {
            $this->documents[] = $document;
            $document->fiche = $this;
        }

        return $this;
    }

    public function removeDocument(Document $document): self
    {
        if ($this->documents->contains($document)) {
            $this->documents->removeElement($document);
            // set the owning side to null (unless already changed)
            if ($document->fiche === $this) {
                $document->fiche =null;
            }
        }

        return $this;
    }

}
