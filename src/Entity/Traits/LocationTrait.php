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
        if (null != $this->getAdresse()) {
            return $this->getAdresse()->getRue();
        }

        return $this->rue;
    }

}
