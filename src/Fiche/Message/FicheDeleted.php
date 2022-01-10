<?php

namespace AcMarche\Bottin\Fiche\Message;

final class FicheDeleted
{
    public function __construct(private int $ficheId)
    {
    }

    public function getFicheId(): int
    {
        return $this->ficheId;
    }
}
