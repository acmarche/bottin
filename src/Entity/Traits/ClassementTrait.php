<?php

namespace AcMarche\Bottin\Entity\Traits;

use AcMarche\Bottin\Entity\Classement;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

trait ClassementTrait
{
    /**
     * @var Classement[]|iterable|Collection
     * @ORM\OneToMany(targetEntity=Classement::class, mappedBy="fiche", cascade={"persist", "remove"})
     */
    protected iterable $classements;

    /**
     * @return Collection|Classement[]
     */
    public function getClassements(): Collection
    {
        return $this->classements;
    }

    public function addClassement(Classement $classement): self
    {
        if (!$this->classements->contains($classement)) {
            $this->classements[] = $classement;
            $classement->setFiche($this);
        }

        return $this;
    }

    public function removeClassement(Classement $classement): self
    {
        if ($this->classements->contains($classement)) {
            $this->classements->removeElement($classement);
            // set the owning side to null (unless already changed)
            if ($classement->getFiche() === $this) {
                $classement->setFiche(null);
            }
        }

        return $this;
    }

    public function setClassements(array $classements)
    {
        $this->classements = $classements;
    }
}
