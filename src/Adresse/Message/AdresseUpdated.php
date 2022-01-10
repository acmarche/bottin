<?php

namespace AcMarche\Bottin\Adresse\Message;

class AdresseUpdated
{
    public function __construct(private int $adresseId, private ?string $oldRue)
    {
    }

    public function getAdresseId(): int
    {
        return $this->adresseId;
    }

    public function getOldRue(): ?string
    {
        return $this->oldRue;
    }
}
