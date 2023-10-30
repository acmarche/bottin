<?php

namespace AcMarche\Bottin\Entity;

use AcMarche\Bottin\Entity\Traits\IdTrait;
use AcMarche\Bottin\Repository\SituationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SituationRepository::class)]
#[ORM\Table(name: 'situation')]
class Situation implements \Stringable
{
    use IdTrait;
    #[Assert\NotBlank]
    #[ORM\Column(type: 'string', nullable: false)]
    public string $name;

    /**
     * @var Fiche[]
     */
    #[ORM\ManyToMany(targetEntity: Fiche::class, mappedBy: 'situations')]
    #[ORM\OrderBy(value: ['societe' => 'ASC'])]
    public iterable $fiches = [];

    public function __construct()
    {
        $this->fiches = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function addFiche(Fiche $fiche): self
    {
        if (!$this->fiches->contains($fiche)) {
            $this->fiches[] = $fiche;
            $fiche->addSituation($this);
        }

        return $this;
    }

    public function removeFiche(Fiche $fiche): self
    {
        if ($this->fiches->contains($fiche)) {
            $this->fiches->removeElement($fiche);
            $fiche->removeSituation($this);
        }

        return $this;
    }

}
