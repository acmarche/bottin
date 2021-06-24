<?php

namespace AcMarche\Bottin\Localite\Message;

class LocaliteCreated
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
