<?php

namespace AcMarche\Bottin\Entity\Traits;

use AcMarche\Bottin\Entity\Classement;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

trait ClassementTrait
{
    /**
     * @var Classement[]|Collection
     */
    #[ORM\OneToMany(targetEntity: Classement::class, mappedBy: 'fiche', cascade: ['persist', 'remove'])]
    public iterable $classements;

    public function addClassement(Classement $classement): self
    {
        if (!$this->classements->contains($classement)) {
            $this->classements[] = $classement;
            $classement->fiche = $this;
        }

        return $this;
    }

    public function removeClassement(Classement $classement): self
    {
        if ($this->classements->contains($classement)) {
            $this->classements->removeElement($classement);
            // set the owning side to null (unless already changed)
            if ($classement->fiche === $this) {
                $classement->fiche = null;
            }
        }

        return $this;
    }

}
