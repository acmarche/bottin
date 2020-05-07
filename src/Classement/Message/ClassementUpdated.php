<?php

namespace AcMarche\Bottin\Classement\Message;

class ClassementUpdated
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
