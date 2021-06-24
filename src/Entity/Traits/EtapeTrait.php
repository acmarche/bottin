<?php

namespace AcMarche\Bottin\Entity\Traits;

trait EtapeTrait
{
    private int $etape = 1;

    public function getEtape(): int
    {
        return $this->etape;
    }

    public function setEtape(int $etape): void
    {
        $this->etape = $etape;
    }
}
