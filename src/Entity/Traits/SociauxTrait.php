<?php

namespace AcMarche\Bottin\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait SociauxTrait
{
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected ?string $facebook = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected ?string $twitter = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected ?string $instagram = null;

    public function getFacebook(): ?string
    {
        return $this->facebook;
    }

    public function setFacebook(?string $facebook): self
    {
        $this->facebook = $facebook;

        return $this;
    }

    public function getTwitter(): ?string
    {
        return $this->twitter;
    }

    public function setTwitter(?string $twitter): self
    {
        $this->twitter = $twitter;

        return $this;
    }

    public function getInstagram(): ?string
    {
        return $this->instagram;
    }

    public function setInstagram(?string $instagram): void
    {
        $this->instagram = $instagram;
    }
}
