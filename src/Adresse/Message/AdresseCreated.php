<?php

namespace AcMarche\Bottin\Adresse\Message;

class AdresseCreated
{
    public function __construct(private readonly int $adresseId)
    {
    }

    public function getAdresseId(): int
    {
        return $this->adresseId;
    }
}
