<?php

namespace AcMarche\Bottin\Classement\Message;

class ClassementUpdated
{
    public function __construct(private int $ficheId, private int $classementId)
    {
    }

    public function getFicheId(): int
    {
        return $this->ficheId;
    }

    public function getClassementId(): int
    {
        return $this->classementId;
    }
}
