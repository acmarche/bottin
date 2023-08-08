<?php

namespace AcMarche\Bottin\Localite\Message;

class LocaliteUpdated
{
    public function __construct(private readonly int $localiteId)
    {
    }

    public function getLocaliteId(): int
    {
        return $this->localiteId;
    }
}
