<?php

namespace AcMarche\Bottin\Classement\Message;

class ClassementDeleted
{
    private int $ficheId;
    private int $classementId;
    private int $categoryId;

    public function __construct(int $ficheId, int $classementId, int $categoryId)
    {
        $this->ficheId = $ficheId;
        $this->classementId = $classementId;
        $this->categoryId = $categoryId;
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
