<?php

namespace AcMarche\Bottin\Entity\Traits;

trait EnfantTrait
{
    private array $enfants = [];

    public function getEnfants(): array
    {
        return $this->enfants;
    }

    public function setEnfants(array $enfants): void
    {
        $this->enfants = $enfants;
    }
}
