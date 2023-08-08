<?php

namespace AcMarche\Bottin\Localite\Message;

class LocaliteCreated
{
    public function __construct(private readonly int $localiteId)
    {
    }

    public function getLocaliteId(): int
    {
        return $this->localiteId;
    }
}
