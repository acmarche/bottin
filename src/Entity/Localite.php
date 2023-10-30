<?php

namespace AcMarche\Bottin\Entity;

use AcMarche\Bottin\Entity\Traits\IdTrait;
use AcMarche\Bottin\Repository\LocaliteRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LocaliteRepository::class)]
class Localite implements \Stringable
{
    use IdTrait;

    #[ORM\Column(type: 'string', length: 150, nullable: false)]
    public ?string $nom = null;

    public array $fiches = [];

    public function __toString(): string
    {
        return $this->nom;
    }
}
