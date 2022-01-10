<?php

namespace AcMarche\Bottin\Localite\Message;

class LocaliteCreated
{
    public function __construct(private int $localiteId)
    {
    }

    public function getLocaliteId(): int
    {
        return $this->localiteId;
    }
}
