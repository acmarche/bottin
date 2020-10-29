<?php

namespace AcMarche\Bottin\Entity\Traits;

trait LocationTrait
{
    /**
     * @var string|null
     */
    protected $rue;

    /**
     * @var string|null
     */
    protected $numero;

    /**
     * @var int|null
     */
    protected $cp;

    /**
     * @var string|null
     */
    protected $localite;

    /**
     * @var string|null
     */
    protected $longitude;

    /**
     * @var string|null
     */
    protected $latitude;

    public function getRue(): ?string
    {
        if ($this->getAdresse() != null) {
            return $this->getAdresse()->getRue();
        }

        return $this->rue;
    }

    public function setRue(string $rue): self
    {
        $this->rue = $rue;

        return $this;
    }

    public function getNumero(): ?string
    {
        if ($this->getAdresse() != null) {
            return $this->getAdresse()->getNumero();
        }

        return $this->numero;
    }

    public function setNumero(?string $numero): self
    {
        $this->numero = $numero;

        return $this;
    }

    public function getCp(): ?int
    {
        if ($this->getAdresse() != null) {
            return $this->getAdresse()->getCp();
        }

        return $this->cp;
    }

    public function setCp(int $cp): self
    {
        $this->cp = $cp;

        return $this;
    }

    public function getLocalite(): ?string
    {
        if ($this->getAdresse() != null) {
            return $this->getAdresse()->getLocalite();
        }

        return $this->localite;
    }

    public function setLocalite(string $localite): self
    {
        $this->localite = $localite;

        return $this;
    }

    public function getLongitude(): ?string
    {
        if ($this->getAdresse() != null) {
            return $this->getAdresse()->getLongitude();
        }

        return $this->longitude;
    }

    public function setLongitude(?string $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getLatitude(): ?string
    {
        if ($this->getAdresse() != null) {
            return $this->getAdresse()->getLatitude();
        }

        return $this->latitude;
    }

    public function setLatitude(?string $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }
}
