<?php

namespace AcMarche\Bottin\Classement\Message;

class ClassementUpdated
{
    public function __construct(private readonly int $ficheId, private readonly int $classementId)
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
