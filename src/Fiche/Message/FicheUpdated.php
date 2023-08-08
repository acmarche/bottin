<?php

namespace AcMarche\Bottin\Fiche\Message;

class FicheUpdated
{
    public function __construct(private readonly int $ficheId, private readonly ?string $oldAddress)
    {
    }

    public function getFicheId(): int
    {
        return $this->ficheId;
    }

    public function getOldAddress(): ?string
    {
        return $this->oldAddress;
    }
}
