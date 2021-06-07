<?php

namespace AcMarche\Bottin\Fiche\Message;

class FicheCreated
{
    private int $ficheId;

    public function __construct(int $ficheId)
    {
        $this->ficheId = $ficheId;
    }

    public function getFicheId(): int
    {
        return $this->ficheId;
    }

}
