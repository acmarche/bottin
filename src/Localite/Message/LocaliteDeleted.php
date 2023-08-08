<?php

namespace AcMarche\Bottin\Localite\Message;

final class LocaliteDeleted
{
    public function __construct(private readonly int $localiteId)
    {
    }

    public function getLocaliteId(): int
    {
        return $this->localiteId;
    }
}
