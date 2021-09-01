<?php

namespace AcMarche\Bottin\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait EnabledTrait
{
    /**
     * @ORM\Column(type="boolean")
     */
    protected bool $enabled = true;

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }
}
