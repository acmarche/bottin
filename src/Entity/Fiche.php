<?php

namespace AcMarche\Bottin\Entity;

use AcMarche\Bottin\Entity\Traits\AdminTrait;
use AcMarche\Bottin\Entity\Traits\CapTrait;
use AcMarche\Bottin\Entity\Traits\ClassementTrait;
use AcMarche\Bottin\Entity\Traits\ContactTrait;
use AcMarche\Bottin\Entity\Traits\DemandeTrait;
use AcMarche\Bottin\Entity\Traits\DocumentsTrait;
use AcMarche\Bottin\Entity\Traits\EcommerceTrait;
use AcMarche\Bottin\Entity\Traits\EnabledTrait;
use AcMarche\Bottin\Entity\Traits\EtapeTrait;
use AcMarche\Bottin\Entity\Traits\HoraireTrait;
use AcMarche\Bottin\Entity\Traits\IdTrait;
use AcMarche\Bottin\Entity\Traits\ImageTrait;
use AcMarche\Bottin\Entity\Traits\InformationTrait;
use AcMarche\Bottin\Entity\Traits\LocationTrait;
use AcMarche\Bottin\Entity\Traits\PdvTrait;
use AcMarche\Bottin\Entity\Traits\SituationsTrait;
use AcMarche\Bottin\Entity\Traits\SociauxTrait;
use AcMarche\Bottin\Entity\Traits\TagTrait;
use AcMarche\Bottin\Entity\Traits\TokenTrait;
use AcMarche\Bottin\Location\LocationAbleInterface;
use AcMarche\Bottin\Repository\FicheRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\SluggableInterface;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Sluggable\SluggableTrait;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: FicheRepository::class)]
#[ORM\Table(name: 'fiche')]
class Fiche implements SluggableInterface, TimestampableInterface, LocationAbleInterface, \Stringable
{
    use AdminTrait;
    use CapTrait;
    use ClassementTrait;
    use ContactTrait;
    use DemandeTrait;
    use DocumentsTrait;
    use EcommerceTrait;
    use EnabledTrait;
    use EtapeTrait;
    use HoraireTrait;
    use IdTrait;
    use ImageTrait;
    use InformationTrait;
    use LocationTrait;
    use PdvTrait;
    use SituationsTrait;
    use SluggableTrait;
    use SociauxTrait;
    use TagTrait;
    use TimestampableTrait;
    use TokenTrait;

    #[Assert\NotBlank]
    #[ORM\Column(type: 'string', nullable: false)]
    public ?string $societe = null;

    #[ORM\Column(type: 'string', nullable: true)]
    public ?string $rue = null;

    #[ORM\Column(type: 'string', nullable: true)]
    public ?string $numero = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    public ?int $cp = null;

    #[ORM\Column(type: 'string', nullable: true)]
    public ?string $localite = null;

    #[ORM\Column(type: 'string', nullable: true)]
    public ?string $telephone = null;

    #[ORM\Column(type: 'string', nullable: true)]
    public ?string $telephone_autre = null;

    #[ORM\Column(type: 'string', nullable: true)]
    public ?string $fax = null;

    #[ORM\Column(type: 'string', nullable: true)]
    public ?string $gsm = null;

    #[ORM\Column(type: 'string', nullable: true)]
    public ?string $website = null;

    #[ORM\Column(type: 'string', nullable: true)]
    public ?string $email = null;

    #[ORM\Column(type: 'string', nullable: true)]
    public ?string $longitude = null;

    #[ORM\Column(type: 'string', nullable: true)]
    public ?string $latitude = null;

    #[ORM\Column(type: 'boolean', options: ['default' => 0])]
    public bool $centreville = false;

    #[ORM\Column(type: 'boolean', options: ['default' => 0])]
    public bool $midi = false;

    #[ORM\Column(type: 'boolean', options: ['default' => 0])]
    public bool $pmr = false;

    #[ORM\Column(type: 'integer', nullable: true)]
    public ?int $ftlb = null;

    #[ORM\Column(type: 'string', nullable: true)]
    public ?string $user = null;

    #[ORM\ManyToOne(targetEntity: Adresse::class, inversedBy: 'fiches')]
    public ?Adresse $adresse = null;

    #[ORM\Column(type: 'string', nullable: true)]
    public ?string $numero_tva = null;

    #[ORM\OneToMany(targetEntity: History::class, mappedBy: 'fiche', cascade: ['remove'])]
    #[ORM\JoinColumn(nullable: false)]
    public ?iterable $histories;

    public array $classementsFull = [];
    public array $metas = [];
    public int $root = 511;

    public function __construct()
    {
        $this->classements = new ArrayCollection();
        $this->horaires = new ArrayCollection();
        $this->images = new ArrayCollection();
        $this->documents = new ArrayCollection();
        $this->demandes = new ArrayCollection();
        $this->situations = new ArrayCollection();
        $this->histories = new ArrayCollection();
    }

    public function getSluggableFields(): array
    {
        return ['societe'];
    }

    public function shouldGenerateUniqueSlugs(): bool
    {
        return true;
    }

    public function image(): ?string
    {
        if ((is_countable($this->images) ? \count($this->images) : 0) > 0) {
            return $this->images[0]->imageName;
        }

        return null;
    }

    public function imageFile(): ?FicheImage
    {
        if ((is_countable($this->images) ? \count($this->images) : 0) > 0) {
            return $this->images[0];
        }

        return null;
    }

    public function hasCategory(int $categoryId): bool
    {
        foreach ($this->classements as $classement) {
            if ($classement->getId() === $categoryId) {
                return true;
            }
        }

        return false;
    }

    public function __toString(): string
    {
        return $this->societe;
    }

    public function getAdresse(): ?Adresse
    {
        return $this->adresse;
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

    public function getTelephoneAutre(): ?string
    {
        return $this->telephone_autre;
    }

    public function getNumeroTva(): ?string
    {
        return $this->numero_tva;
    }

}
