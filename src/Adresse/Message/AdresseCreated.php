<?php

namespace AcMarche\Bottin\Adresse\Message;

class AdresseCreated
{
    private $adresseId;

    public function __construct(int $adresseId)
    {
        $this->adresseId = $adresseId;
    }

    public function getAdresseId(): int
    {
        return $this->adresseId;
    }

}
