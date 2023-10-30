<?php

namespace AcMarche\Bottin\Entity;

use AcMarche\Bottin\Entity\Traits\IdTrait;
use AcMarche\Bottin\Meta\Repository\MetaDataRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: MetaDataRepository::class)]
#[ORM\UniqueConstraint(columns: ['name', 'fiche_id'])]
#[UniqueEntity(fields: ['name', 'fiche'], message: "Cette fiche a déjà ce méta data")]
#[ORM\Table(name: 'meta_data')]
class MetaData
{
    use IdTrait;

    #[ORM\Column(nullable: false)]
    public string $name;

    #[ORM\ManyToOne(targetEntity: Fiche::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    public Fiche $fiche;

    #[ORM\Column(nullable: false)]
    public ?string $value;

    public function __construct(Fiche $fiche, string $name, ?string $value)
    {
        $this->fiche = $fiche;
        $this->name = $name;
        $this->value = $value;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}