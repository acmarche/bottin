<?php

namespace AcMarche\Bottin\Entity\Traits;

trait CapTrait
{
    private bool $cap = false;

    public function isCap(): bool
    {
        return $this->cap;
    }

    public function setCap(bool $cap): void
    {
        $this->cap = $cap;
    }
}
