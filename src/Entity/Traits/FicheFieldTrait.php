<?php

namespace AcMarche\Bottin\Entity\Traits;

use AcMarche\Bottin\Entity\Fiche;

trait FicheFieldTrait
{
    protected ?Fiche $fiche = null;

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
