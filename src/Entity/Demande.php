<?php

namespace AcMarche\Bottin\Entity;

use AcMarche\Bottin\Entity\Traits\IdTrait;
use AcMarche\Bottin\Repository\DemandeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Stringable;

#[ORM\Entity(repositoryClass: DemandeRepository::class)]
#[ORM\Table(name: 'demande')]
class Demande implements TimestampableInterface, Stringable
{
    use TimestampableTrait;
    use IdTrait;
    #[ORM\ManyToOne(targetEntity: 'Fiche', inversedBy: 'demandes')]
    #[ORM\JoinColumn(nullable: false)]
    protected ?Fiche $fiche = null;
    #[ORM\Column(type: 'string', nullable: true)]
    protected ?string $traiter_by = null;
    #[ORM\Column(type: 'boolean', options: ['default' => 0])]
    protected bool $traiter = false;
    /**
     * @var DemandeMeta[]|ArrayCollection|iterable
     */
    #[ORM\OneToMany(targetEntity: DemandeMeta::class, mappedBy: 'demande', cascade: ['persist', 'remove'])]
    protected iterable $metas;

    public function __construct()
    {
        $this->metas = new ArrayCollection();
    }

    public function __toString(): string
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

    public function getTraiter(): bool
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
    public function getMetas(): iterable
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
