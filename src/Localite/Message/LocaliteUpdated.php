<?php

namespace AcMarche\Bottin\Localite\Message;

class LocaliteUpdated
{
    public function __construct(private int $localiteId)
    {
    }

    public function getLocaliteId(): int
    {
        return $this->localiteId;
    }
}
