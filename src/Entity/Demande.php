<?php

namespace AcMarche\Bottin\Entity;

use AcMarche\Bottin\Entity\Traits\IdTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;

/**
 * @ORM\Entity(repositoryClass="AcMarche\Bottin\Repository\DemandeRepository")
 * @ORM\Table(name="demande")
 */
class Demande implements TimestampableInterface
{
    use TimestampableTrait;
    use IdTrait;

    /**
     * @var Fiche|null
     * @ORM\ManyToOne(targetEntity="Fiche", inversedBy="demandes")
     * @ORM\JoinColumn(nullable=false))
     */
    protected $fiche;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     */
    protected $traiter_by;

    /**
     * @var bool
     * @ORM\Column(type="boolean", options={"default": 0})
     */
    protected $traiter = false;

    /**
     * @var DemandeMeta[]
     * @ORM\OneToMany(targetEntity="AcMarche\Bottin\Entity\DemandeMeta", mappedBy="demande", cascade={"persist", "remove"})
     */
    protected $metas;

    public function __construct()
    {
        $this->metas = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getFiche()->getSociete();
    }

    public function getTraiterBy(): ?string
    {
        return $this->traiter_by;
    }

    public function setTraiterBy(?string $traiter_by): self
    {
        $this->traiter_by = $traiter_by;

        return $this;
    }

    public function getTraiter(): ?bool
    {
        return $this->traiter;
    }

    public function setTraiter(bool $traiter): self
    {
        $this->traiter = $traiter;

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

    /**
     * @return Collection|DemandeMeta[]
     */
    public function getMetas(): Collection
    {
        return $this->metas;
    }

    public function addMeta(DemandeMeta $meta): self
    {
        if (!$this->metas->contains($meta)) {
            $this->metas[] = $meta;
            $meta->setDemande($this);
        }

        return $this;
    }

    public function removeMeta(DemandeMeta $meta): self
    {
        if ($this->metas->contains($meta)) {
            $this->metas->removeElement($meta);
            // set the owning side to null (unless already changed)
            if ($meta->getDemande() === $this) {
                $meta->setDemande(null);
            }
        }

        return $this;
    }
}
