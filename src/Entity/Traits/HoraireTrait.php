<?php


namespace AcMarche\Bottin\Entity\Traits;

use AcMarche\Bottin\Entity\Horaire;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

trait HoraireTrait
{
    /**
     * @var Horaire[]|iterable|Collection
     * @ORM\OneToMany(targetEntity="Horaire", mappedBy="fiche", cascade={"persist", "remove"})
     * @ORM\OrderBy({"day": "ASC"})
     */
    protected iterable $horaires;

    /**
     * @return Collection|Horaire[]
     */
    public function getHoraires(): Collection
    {
        return $this->horaires;
    }

    public function addHoraire(Horaire $horaire): self
    {
        if (!$this->horaires->contains($horaire)) {
            $this->horaires[] = $horaire;
            $horaire->setFiche($this);
        }

        return $this;
    }

    public function removeHoraire(Horaire $horaire): self
    {
        if ($this->horaires->contains($horaire)) {
            $this->horaires->removeElement($horaire);
            // set the owning side to null (unless already changed)
            if ($horaire->getFiche() === $this) {
                $horaire->setFiche(null);
            }
        }

        return $this;
    }

    public function setHoraires(array $horaires)
    {
        $this->horaires = $horaires;
    }
}
