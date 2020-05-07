<?php

namespace AcMarche\Bottin\Fiche\Message;

final class FicheDeleted
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
