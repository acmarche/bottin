<?php


namespace AcMarche\Bottin\Entity\Traits;


use AcMarche\Bottin\Entity\Fiche;
use Doctrine\ORM\Mapping as ORM;

trait FicheFieldTrait
{
    protected ?Fiche $fiche;

    public function getFiche(): ?Fiche
    {
        return $this->fiche;
    }

    public function setFiche(?Fiche $fiche): self
    {
        $this->fiche = $fiche;

        return $this;
    }
}
