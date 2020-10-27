<?php

namespace AcMarche\Bottin\Entity;

use AcMarche\Bottin\Entity\Traits\LocationTrait;
use AcMarche\Bottin\Location\LocationAbleInterface;
use AcMarche\Bottin\Repository\AdresseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\SluggableInterface;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Sluggable\SluggableTrait;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;

/**
 * @ORM\Entity(repositoryClass=AdresseRepository::class)
 */
class Adresse implements SluggableInterface, TimestampableInterface, LocationAbleInterface
{
    use SluggableTrait;
    use TimestampableTrait;
    use LocationTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=150)
     */
    private $nom;
    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=false)
     */
    protected $rue;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     */
    protected $numero;

    /**
     * @var int|null
     * @ORM\Column(type="integer", nullable=false)
     */
    protected $cp;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=false)
     */
    protected $localite;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     */
    protected $longitude;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     */
    protected $latitude;

    /**
     * @var Fiche[]
     * @ORM\OneToMany(targetEntity="AcMarche\Bottin\Entity\Fiche", mappedBy="adresse")
     */
    protected $fiches;

    public function __construct()
    {
        $this->fiches = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->nom;
    }

    public function getSluggableFields(): array
    {
        return ['nom'];
    }

    public function shouldGenerateUniqueSlugs(): bool
    {
        return true;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }



    /**
     * @return Collection|Fiche[]
     */
    public function getFiches(): Collection
    {
        return $this->fiches;
    }

    public function addFich(Fiche $fiche): self
    {
        if (!$this->fiches->contains($fiche)) {
            $this->fiches[] = $fiche;
            $fiche->setAdresse($this);
        }

        return $this;
    }

    public function removeFiche(Fiche $fiche): self
    {
        if ($this->fiches->contains($fiche)) {
            $this->fiches->removeElement($fiche);
            // set the owning side to null (unless already changed)
            if ($fiche->getAdresse() === $this) {
                $fiche->setAdresse(null);
            }
        }

        return $this;
    }

}
