<?php

namespace AcMarche\Bottin\Adresse\Message;

final class AdresseDeleted
{
    public function __construct(private readonly int $adresseId)
    {
    }

    public function getAdresseId(): int
    {
        return $this->adresseId;
    }
}
