<?php

namespace AcMarche\Bottin\Entity\Traits;

use AcMarche\Bottin\Entity\Demande;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

trait DemandeTrait
{
    /**
     * @var Demande[]|iterable|Collection
     */
    #[ORM\OneToMany(targetEntity: Demande::class, mappedBy: 'fiche', cascade: ['persist', 'remove'])]
    protected iterable $demandes;

    /**
     * @return Collection|Demande[]
     */
    public function getDemandes(): Collection
    {
        return $this->demandes;
    }

    public function addDemande(Demande $demande): self
    {
        if (!$this->demandes->contains($demande)) {
            $this->demandes[] = $demande;
            $demande->setFiche($this);
        }

        return $this;
    }

    public function removeDemande(Demande $demande): self
    {
        if ($this->demandes->contains($demande)) {
            $this->demandes->removeElement($demande);
            // set the owning side to null (unless already changed)
            if ($demande->getFiche() === $this) {
                $demande->setFiche(null);
            }
        }

        return $this;
    }
}
