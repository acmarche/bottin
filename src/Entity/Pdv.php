<?php

namespace AcMarche\Bottin\Entity;

use AcMarche\Bottin\Entity\Traits\IdTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AcMarche\Bottin\Repository\PdvRepository")
 * @ORM\Table(name="pdv")
 */
class Pdv
{
    use IdTrait;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=false)
     * @Assert\NotBlank
     */
    protected $intitule;

    /**
     * @var Fiche[]
     * @ORM\OneToMany(targetEntity="AcMarche\Bottin\Entity\Fiche", mappedBy="pdv")
     * @ORM\OrderBy({"societe": "ASC"})
     */
    protected $fiches;

    public function __construct()
    {
        $this->fiches = new ArrayCollection();
    }

    public function __toString()
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
    public function getFiches(): Collection
    {
        return $this->fiches;
    }

    public function addFich(Fiche $fich): self
    {
        if (!$this->fiches->contains($fich)) {
            $this->fiches[] = $fich;
            $fich->setPdv($this);
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
