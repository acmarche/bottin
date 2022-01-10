<?php

namespace AcMarche\Bottin\Fiche\Message;

class FicheCreated
{
    public function __construct(private int $ficheId)
    {
    }

    public function getFicheId(): int
    {
        return $this->ficheId;
    }
}
