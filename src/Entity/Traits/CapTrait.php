<?php


namespace AcMarche\Bottin\Entity\Traits;


trait CapTrait
{
    /**
     * @var boolean
     */
    private $cap = false;

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
