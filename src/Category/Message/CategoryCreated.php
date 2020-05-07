<?php

namespace AcMarche\Bottin\Category\Message;

class CategoryCreated
{
    private $categoryId;

    public function __construct(int $categoryId)
    {
        $this->categoryId = $categoryId;
    }

    public function getCategoryId(): int
    {
        return $this->categoryId;
    }

}
