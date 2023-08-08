<?php

namespace AcMarche\Bottin\Classement\Message;

class ClassementDeleted
{
    public function __construct(private readonly int $ficheId, private readonly int $classementId, private readonly int $categoryId)
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
