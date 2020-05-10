<?php

namespace AcMarche\Bottin\Hades;

use AcMarche\Bottin\Hades\Entity\Hotel;

class Response
{
    /**
     * @var Hotel[]
     */
    private $item = [];

    /**
     * @return Hotel[]
     */
    public function getItem(): array
    {
        return $this->item;
    }

    /**
     * @param Hotel[] $item
     */
    public function setItem(array $item): void
    {
        $this->item = $item;
    }

    public function addItem(Hotel $person): void
    {
        $this->item[] = $person;
    }

}
