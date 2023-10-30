<?php

namespace AcMarche\Bottin\Entity;

use AcMarche\Bottin\Entity\Traits\IdTrait;
use AcMarche\Bottin\Repository\ClassementRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;

#[UniqueEntity(fields: ['fiche', 'category'], message: 'Déjà dans ce classement')]
#[ORM\Entity(repositoryClass: ClassementRepository::class)]
#[ORM\Table(name: 'classements')]
#[ORM\UniqueConstraint(name: 'classement_idx', columns: ['fiche_id', 'category_id'])]
class Classement implements \Stringable
{
    use IdTrait;

    #[Groups(groups: ['read', 'write'])]
    #[ORM\Column(type: 'boolean')]
    public bool $principal = false;

    #[ORM\ManyToOne(targetEntity: 'Fiche', inversedBy: 'classements')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    public ?Fiche $fiche;

    #[ORM\ManyToOne(targetEntity: 'Category', inversedBy: 'classements')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    public ?Category $category;

    public function __construct(Fiche $fiche, Category $category)
    {
        $this->fiche = $fiche;
        $this->category = $category;
    }

    public function __toString(): string
    {
        return $this->category->name;
    }

    public function __get($prop)
    {
        return $this->$prop;
    }

    public function __isset($prop): bool
    {
        return property_exists($this, 'prop') && $this->$prop !== null;
    }

}
