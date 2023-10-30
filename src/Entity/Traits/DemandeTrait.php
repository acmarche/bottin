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
    public iterable $demandes;

    public function addDemande(Demande $demande): self
    {
        if (!$this->demandes->contains($demande)) {
            $this->demandes[] = $demande;
            $demande->fiche = $this;
        }

        return $this;
    }

    public function removeDemande(Demande $demande): self
    {
        if ($this->demandes->contains($demande)) {
            $this->demandes->removeElement($demande);
            // set the owning side to null (unless already changed)
            if ($demande->fiche === $this) {
                $demande->fiche = null;
            }
        }

        return $this;
    }
}
