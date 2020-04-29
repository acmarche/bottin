<?php


namespace AcMarche\Bottin\Entity\Traits;


trait SociauxTrait
{

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     */
    protected $facebook;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     */
    protected $twitter;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     */
    protected $instagram;


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

    /**
     * @return string|null
     */
    public function getInstagram(): ?string
    {
        return $this->instagram;
    }

    /**
     * @param string|null $instagram
     */
    public function setInstagram(?string $instagram): void
    {
        $this->instagram = $instagram;
    }

}
