<?php

namespace AcMarche\Bottin\Category\Message;

class CategoryDeleted
{
    public function __construct(private int $categoryId)
    {
    }

    public function getCategoryId(): int
    {
        return $this->categoryId;
    }
}
