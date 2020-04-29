<?php

namespace AcMarche\Bottin\Message;

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
