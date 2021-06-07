<?php

namespace AcMarche\Bottin\Entity\Traits;

trait LocationTrait
{
    protected ?string $rue;

    protected ?string $numero;

    protected ?int $cp;

    protected ?string $localite;

    protected ?string $longitude;

    protected ?string $latitude;

    public function getRue(): ?string
    {
        if (null != $this->getAdresse()) {
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
        if (null != $this->getAdresse()) {
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
        if (null != $this->getAdresse()) {
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
        if (null != $this->getAdresse()) {
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
        if (null != $this->getAdresse()) {
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
        if (null != $this->getAdresse()) {
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
