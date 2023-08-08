<?php

namespace AcMarche\Bottin\Fiche\Message;

final class FicheDeleted
{
    public function __construct(private readonly int $ficheId)
    {
    }

    public function getFicheId(): int
    {
        return $this->ficheId;
    }
}
