<?php


namespace AcMarche\Bottin\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Situation
 * @package AcMarche\Bottin\Entity
 * @ORM\Entity(repositoryClass="AcMarche\Bottin\Repository\SituationRepository")
 * @ORM\Table(name="situation")
 */
class Situation
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false)
     * @Assert\NotBlank
     */
    protected $name;

    /**
     * @var Fiche[]
     * @ORM\ManyToMany(targetEntity="AcMarche\Bottin\Entity\Fiche", mappedBy="situations")
     * @ORM\OrderBy({"societe": "ASC"})
     */
    protected $fiches;

    public function __construct()
    {
        $this->fiches = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection|Fiche[]
     */
    public function getFiches(): Collection
    {
        return $this->fiches;
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

    public function addFich(Fiche $fich): self
    {
        if (!$this->fiches->contains($fich)) {
            $this->fiches[] = $fich;
            $fich->addSituation($this);
        }

        return $this;
    }

    public function removeFich(Fiche $fich): self
    {
        if ($this->fiches->contains($fich)) {
            $this->fiches->removeElement($fich);
            $fich->removeSituation($this);
        }

        return $this;
    }

}
