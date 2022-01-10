<?php

namespace AcMarche\Bottin\Classement\Message;

class ClassementDeleted
{
    public function __construct(private int $ficheId, private int $classementId, private int $categoryId)
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

    public function getCategoryId(): int
    {
        return $this->categoryId;
    }
}
