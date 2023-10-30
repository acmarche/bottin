<?php

namespace AcMarche\Bottin\Entity;

use AcMarche\Bottin\Entity\Traits\IdTrait;
use AcMarche\Bottin\Repository\DemandeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;

#[ORM\Entity(repositoryClass: DemandeRepository::class)]
#[ORM\Table(name: 'demande')]
class Demande implements TimestampableInterface, \Stringable
{
    use IdTrait;
    use TimestampableTrait;

    #[ORM\ManyToOne(targetEntity: 'Fiche', inversedBy: 'demandes')]
    #[ORM\JoinColumn(nullable: false)]
    public ?Fiche $fiche = null;

    #[ORM\Column(type: 'string', nullable: true)]
    public ?string $traiter_by = null;

    #[ORM\Column(type: 'boolean', options: ['default' => 0])]
    public bool $traiter = false;

    /**
     * @var DemandeMeta[]|ArrayCollection|iterable
     */
    #[ORM\OneToMany(targetEntity: DemandeMeta::class, mappedBy: 'demande', cascade: ['persist', 'remove'])]
    public iterable $metas;

    public function __construct(Fiche $fiche)
    {
        $this->metas = new ArrayCollection();
        $this->fiche = $fiche;
    }

    public function __toString(): string
    {
        return $this->fiche->societe;
    }

    public function addMeta(DemandeMeta $meta): self
    {
        if (!$this->metas->contains($meta)) {
            $this->metas[] = $meta;
            $meta->demande = $this;
        }

        return $this;
    }

    public function removeMeta(DemandeMeta $meta): self
    {
        if ($this->metas->contains($meta)) {
            $this->metas->removeElement($meta);
            // set the owning side to null (unless already changed)
            if ($meta->demande === $this) {
                $meta->demande = null;
            }
        }

        return $this;
    }
}
