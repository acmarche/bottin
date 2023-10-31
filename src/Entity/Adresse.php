<?php

namespace AcMarche\Bottin\Entity;

use AcMarche\Bottin\Entity\Traits\IdTrait;
use AcMarche\Bottin\Entity\Traits\LocationTrait;
use AcMarche\Bottin\Location\LocationAbleInterface;
use AcMarche\Bottin\Repository\AdresseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\SluggableInterface;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Sluggable\SluggableTrait;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;

#[ORM\Entity(repositoryClass: AdresseRepository::class)]
class Adresse implements SluggableInterface, TimestampableInterface, LocationAbleInterface, \Stringable
{
    use IdTrait;
    use LocationTrait;
    use SluggableTrait;
    use TimestampableTrait;

    #[ORM\Column(type: 'string', length: 150, nullable: false)]
    public ?string $nom = null;

    #[ORM\Column(type: 'string', nullable: false)]
    public ?string $rue = null;

    #[ORM\Column(type: 'string', nullable: true)]
    public ?string $numero = null;

    #[ORM\Column(type: 'integer', nullable: false)]
    public ?int $cp = null;

    #[ORM\Column(type: 'string', nullable: false)]
    public ?string $localite = null;

    #[ORM\Column(type: 'string', nullable: true)]
    public ?string $longitude = null;

    #[ORM\Column(type: 'string', nullable: true)]
    public ?string $latitude = null;

    /**
     * @var Fiche[]
     */
    #[ORM\OneToMany(targetEntity: Fiche::class, mappedBy: 'adresse')]
    public iterable $fiches = [];

    /**
     * Fake pour location convert.
     */
    public function getAdresse(): ?self
    {
        return null;
    }

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

    public function getNumero(): ?string
    {
        return $this->numero;
    }

    public function getCp(): ?int
    {
        return $this->cp;
    }

    public function getLocalite()
    {
        return $this->localite;
    }

    public function getLongitude(): ?string
    {
        return $this->longitude;
    }

    public function getLatitude(): ?string
    {
        return $this->latitude;
    }

    public function addFiche(Fiche $fiche): self
    {
        if (!$this->fiches->contains($fiche)) {
            $this->fiches[] = $fiche;
            $fiche->adresse = $this;
        }

        return $this;
    }

    public function removeFiche(Fiche $fiche): self
    {
        if ($this->fiches->contains($fiche)) {
            $this->fiches->removeElement($fiche);
            // set the owning side to null (unless already changed)
            if ($fiche->adresse === $this) {
                $fiche->adresse = null;
            }
        }

        return $this;
    }
}
