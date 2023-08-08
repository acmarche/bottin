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
    protected bool $principal = false;

    public function __construct(#[ORM\ManyToOne(targetEntity: 'Fiche', inversedBy: 'classements')]
        #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
        protected ?Fiche $fiche, #[ORM\ManyToOne(targetEntity: 'Category', inversedBy: 'classements')]
        #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
        protected ?Category $category)
    {
    }

    public function __toString(): string
    {
        return $this->getCategory()->getName();
    }

    public function __get($prop)
    {
        return $this->$prop;
    }

    public function __isset($prop): bool
    {
        return property_exists($this, 'prop') && $this->$prop !== null;
    }

    public function getPrincipal(): bool
    {
        return $this->principal;
    }

    public function setPrincipal(bool $principal): self
    {
        $this->principal = $principal;

        return $this;
    }

    public function getFiche(): ?Fiche
    {
        return $this->fiche;
    }

    public function setFiche(?Fiche $fiche): self
    {
        $this->fiche = $fiche;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }
}
