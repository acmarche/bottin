<?php

namespace AcMarche\Bottin\Entity;

use AcMarche\Bottin\Entity\Traits\IdTrait;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="AcMarche\Bottin\Repository\ClassementRepository")
 * @ORM\Table(name="classements", uniqueConstraints={
 * @ORM\UniqueConstraint(name="classement_idx", columns={"fiche_id", "category_id"})})
 * @UniqueEntity(fields={"fiche", "category"}, message="Déjà dans ce classement")
 *  @ApiResource(
 *     normalizationContext={"groups"={"read"}},
 *     denormalizationContext={"groups"={"write"}}
 * )
 */
class Classement
{
    use IdTrait;

    /**
     * @ORM\ManyToOne(targetEntity="Fiche", inversedBy="classements")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    protected ?Fiche $fiche;

    /**
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="classements")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    protected ?Category $category;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"read", "write"})
     */
    protected bool $principal = false;

    public function __construct(Fiche $fiche, Category $category)
    {
        $this->fiche = $fiche;
        $this->category = $category;
    }

    public function __toString()
    {
        return $this->getCategory()->getName();
    }

    public function __get($prop)
    {
        return $this->$prop;
    }

    public function __isset($prop): bool
    {
        return isset($this->$prop);
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
