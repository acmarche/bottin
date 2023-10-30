<?php

namespace AcMarche\Bottin\Entity;

use AcMarche\Bottin\Entity\Traits\IdTrait;
use AcMarche\Bottin\Repository\PdvRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PdvRepository::class)]
#[ORM\Table(name: 'pdv')]
class Pdv implements \Stringable
{
    use IdTrait;

    #[Assert\NotBlank]
    #[ORM\Column(type: 'string', nullable: false)]
    public ?string $intitule = null;

    #[ORM\OneToMany(targetEntity: Fiche::class, mappedBy: 'pdv')]
    #[ORM\OrderBy(value: ['societe' => 'ASC'])]
    /**
     * @var Fiche[]
     */
    public iterable $fiches = [];

    public function __construct()
    {
        $this->fiches = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->intitule;
    }

    public function addFiche(Fiche $fiche): self
    {
        if (!$this->fiches->contains($fiche)) {
            $this->fiches[] = $fiche;
            $fiche->pdv = $this;
        }

        return $this;
    }

    public function removeFiche(Fiche $fiche): self
    {
        if ($this->fiches->contains($fiche)) {
            $this->fiches->removeElement($fiche);
            // set the owning side to null (unless already changed)
            if ($fiche->pdv === $this) {
                $fiche->pdv = null;
            }
        }

        return $this;
    }
}
