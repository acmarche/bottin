<?php

namespace AcMarche\Bottin\Entity\Traits;

trait LocationTrait
{
    public ?string $rue = null;

    public ?string $numero = null;

    public ?int $cp = null;

    public ?string $localite = null;

    public ?string $longitude = null;

    public ?string $latitude = null;

    public function getRue(): ?string
    {
        if (null != $this->adresse) {
            return $this->adresse->getRue();
        }

        return $this->rue;
    }

}
