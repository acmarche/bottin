<?php


namespace AcMarche\Bottin\Entity\Traits;

trait CapTrait
{
    private bool $cap = false;

    /**
     * @return bool
     */
    public function isCap(): bool
    {
        return $this->cap;
    }

    /**
     * @param bool $cap
     */
    public function setCap(bool $cap): void
    {
        $this->cap = $cap;
    }
}
