<?php

namespace AcMarche\Bottin\Localite\Message;

class LocaliteUpdated
{
    private int $localiteId;

    public function __construct(int $localiteId)
    {
        $this->localiteId = $localiteId;
    }

    public function getLocaliteId(): int
    {
        return $this->localiteId;
    }
}
