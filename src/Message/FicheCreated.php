<?php

namespace AcMarche\Bottin\Message;

class FicheCreated
{
    private $ficheId;

    public function __construct(int $ficheId)
    {
        $this->ficheId = $ficheId;
    }

    public function getFicheId(): int
    {
        return $this->ficheId;
    }

}
