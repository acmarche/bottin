<?php

namespace AcMarche\Bottin\Entity;

use AcMarche\Bottin\Entity\Traits\IdTrait;
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
use Stringable;

#[ORM\Entity(repositoryClass: AdresseRepository::class)]
class Adresse implements SluggableInterface, TimestampableInterface, LocationAbleInterface, Stringable
{
    use IdTrait;
    use SluggableTrait;
    use TimestampableTrait;
    use LocationTrait;
    #[ORM\Column(type: 'string', length: 150, nullable: false)]
    private ?string $nom = null;

    #[ORM\Column(type: 'string', nullable: false)]
    protected ?string $rue = null;

    #[ORM\Column(type: 'string', nullable: true)]
    protected ?string $numero = null;

    #[ORM\Column(type: 'integer', nullable: false)]
    protected ?int $cp = null;

    #[ORM\Column(type: 'string', nullable: false)]
    protected ?string $localite = null;

    #[ORM\Column(type: 'string', nullable: true)]
    protected ?string $longitude = null;

    #[ORM\Column(type: 'string', nullable: true)]
    protected ?string $latitude = null;

    /**
     * @var Fiche[]
     */
    #[ORM\OneToMany(targetEntity: Fiche::class, mappedBy: 'adresse')]
    protected iterable $fiches = [];

    public function __construct()
    {
        $this->fiches = new ArrayCollection();
    }

    public function __toString(): string
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

    /**
     * Fake pour location convert.
     */
    public function getAdresse(): ?self
    {
        return null;
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
    public function getFiches(): iterable
    {
        return $this->fiches;
    }

    public function addFiche(Fiche $fiche): self
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

    public function addFich(Fiche $fich): self
    {
        if (!$this->fiches->contains($fich)) {
            $this->fiches[] = $fich;
            $fich->setAdresse($this);
        }

        return $this;
    }

    public function removeFich(Fiche $fich): self
    {
        // set the owning side to null (unless already changed)
        if ($this->fiches->removeElement($fich) && $fich->getAdresse() === $this) {
            $fich->setAdresse(null);
        }

        return $this;
    }
}
