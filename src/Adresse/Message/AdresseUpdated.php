<?php

namespace AcMarche\Bottin\Adresse\Message;

class AdresseUpdated
{
    public function __construct(private readonly int $adresseId, private readonly ?string $oldRue)
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
