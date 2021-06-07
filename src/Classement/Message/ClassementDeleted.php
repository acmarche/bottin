<?php

namespace AcMarche\Bottin\Classement\Message;

class ClassementDeleted
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
