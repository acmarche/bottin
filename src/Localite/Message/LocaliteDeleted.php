<?php

namespace AcMarche\Bottin\Localite\Message;

final class LocaliteDeleted
{
    public function __construct(private int $localiteId)
    {
    }

    public function getLocaliteId(): int
    {
        return $this->localiteId;
    }
}
