<?php

namespace AcMarche\Bottin\Fiche\Message;

class FicheUpdated
{
    public function __construct(private int $ficheId, private ?string $oldAddress)
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
