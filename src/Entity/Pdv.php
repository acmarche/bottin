<?php

namespace AcMarche\Bottin\Entity;

use AcMarche\Bottin\Entity\Traits\IdTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Stringable;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AcMarche\Bottin\Repository\PdvRepository")
 * @ORM\Table(name="pdv")
 */
class Pdv implements Stringable
{
    use IdTrait;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    #[Assert\NotBlank]
    protected ?string $intitule = null;

    /**
     * @var Fiche[]
     * @ORM\OneToMany(targetEntity="AcMarche\Bottin\Entity\Fiche", mappedBy="pdv")
     * @ORM\OrderBy({"societe"="ASC"})
     */
    protected iterable $fiches;

    public function __construct()
    {
        $this->fiches = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->getIntitule();
    }

    public function getIntitule(): ?string
    {
        return $this->intitule;
    }

    public function setIntitule(string $intitule): self
    {
        $this->intitule = $intitule;

        return $this;
    }

    /**
     * @return Collection|Fiche[]
     */
    public function getFiches(): iterable
    {
        return $this->fiches;
    }

    public function addFich(Fiche $fiche): self
    {
        if (!$this->fiches->contains($fiche)) {
            $this->fiches[] = $fiche;
            $fiche->setPdv($this);
        }

        return $this;
    }

    public function removeFich(Fiche $fich): self
    {
        if ($this->fiches->contains($fich)) {
            $this->fiches->removeElement($fich);
            // set the owning side to null (unless already changed)
            if ($fich->getPdv() === $this) {
                $fich->setPdv(null);
            }
        }

        return $this;
    }
}
