<?php


namespace AcMarche\Bottin\Entity\Traits;


trait EnabledTrait
{
    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    protected $enabled = true;

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     */
    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }
}
