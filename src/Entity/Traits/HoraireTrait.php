<?php

namespace AcMarche\Bottin\Entity\Traits;

use AcMarche\Bottin\Entity\Horaire;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

trait HoraireTrait
{
    /**
     * @var Horaire[]|iterable|Collection
     */
    #[ORM\OneToMany(targetEntity: 'Horaire', mappedBy: 'fiche', cascade: ['persist', 'remove'])]
    #[ORM\OrderBy(value: ['day' => 'ASC'])]
    public iterable $horaires;

    public function addHoraire(Horaire $horaire): self
    {
        if (!$this->horaires->contains($horaire)) {
            $this->horaires[] = $horaire;
            $horaire->fiche = $this;
        }

        return $this;
    }

    public function removeHoraire(Horaire $horaire): self
    {
        if ($this->horaires->contains($horaire)) {
            $this->horaires->removeElement($horaire);
            // set the owning side to null (unless already changed)
            if ($horaire->fiche === $this) {
                //   $horaire->setFiche(null);
            }
        }

        return $this;
    }
}
