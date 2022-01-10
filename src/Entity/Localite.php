<?php

namespace AcMarche\Bottin\Entity;

use AcMarche\Bottin\Entity\Traits\IdTrait;
use AcMarche\Bottin\Repository\LocaliteRepository;
use Doctrine\ORM\Mapping as ORM;
use Stringable;

#[ORM\Entity(repositoryClass: LocaliteRepository::class)]
class Localite implements Stringable
{
    use IdTrait;
    #[ORM\Column(type: 'string', length: 150, nullable: false)]
    private ?string $nom = null;

    public function __toString(): string
    {
        return $this->nom;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }
}
