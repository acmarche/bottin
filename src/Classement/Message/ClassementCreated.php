<?php

namespace AcMarche\Bottin\Classement\Message;

class ClassementCreated
{
    private int $ficheId;
    private int $classementId;

    public function __construct(int $ficheId, int $classementId)
    {
        $this->ficheId = $ficheId;
        $this->classementId = $classementId;
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
