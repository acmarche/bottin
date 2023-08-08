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
use Stringable;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: FicheRepository::class)]
#[ORM\Table(name: 'fiche')]
class Fiche implements SluggableInterface, TimestampableInterface, LocationAbleInterface, Stringable
{
    use IdTrait;
    use LocationTrait;
    use SluggableTrait;
    use TimestampableTrait;
    use AdminTrait;
    use ClassementTrait;
    use ContactTrait;
    use DemandeTrait;
    use HoraireTrait;
    use ImageTrait;
    use DocumentsTrait;
    use InformationTrait;
    use SociauxTrait;
    use PdvTrait;
    use SituationsTrait;
    use EnabledTrait;
    use CapTrait;
    use EcommerceTrait;
    use TokenTrait;
    use EtapeTrait;
    use TagTrait;

    #[Assert\NotBlank]
    #[ORM\Column(type: 'string', nullable: false)]
    protected ?string $societe = null;

    #[ORM\Column(type: 'string', nullable: true)]
    protected ?string $rue = null;

    #[ORM\Column(type: 'string', nullable: true)]
    protected ?string $numero = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    protected ?int $cp = null;

    #[ORM\Column(type: 'string', nullable: true)]
    protected ?string $localite = null;

    #[ORM\Column(type: 'string', nullable: true)]
    protected ?string $telephone = null;

    #[ORM\Column(type: 'string', nullable: true)]
    protected ?string $telephone_autre = null;

    #[ORM\Column(type: 'string', nullable: true)]
    protected ?string $fax = null;

    #[ORM\Column(type: 'string', nullable: true)]
    protected ?string $gsm = null;

    #[ORM\Column(type: 'string', nullable: true)]
    protected ?string $website = null;

    #[ORM\Column(type: 'string', nullable: true)]
    protected ?string $email = null;

    #[ORM\Column(type: 'string', nullable: true)]
    protected ?string $longitude = null;

    #[ORM\Column(type: 'string', nullable: true)]
    protected ?string $latitude = null;

    #[ORM\Column(type: 'boolean', options: ['default' => 0])]
    protected bool $centreville = false;

    #[ORM\Column(type: 'boolean', options: ['default' => 0])]
    protected bool $midi = false;

    #[ORM\Column(type: 'boolean', options: ['default' => 0])]
    protected bool $pmr = false;

    #[ORM\Column(type: 'integer', nullable: true)]
    protected ?int $ftlb = null;

    #[ORM\Column(type: 'string', nullable: true)]
    protected ?string $user = null;

    /**
     * Utiliser lors de l'ajout d'un classement.
     */
    protected ?int $categoryId = null;

    #[ORM\ManyToOne(targetEntity: Adresse::class, inversedBy: 'fiches')]
    protected ?Adresse $adresse = null;

    #[ORM\Column(type: 'string', nullable: true)]
    protected ?string $numero_tva = null;

    /**
     * Pour cascade.
     */
    #[ORM\OneToMany(targetEntity: History::class, mappedBy: 'fiche', cascade: ['remove'])]
    #[ORM\JoinColumn(nullable: false)]
    protected ?iterable $histories;

    public array $classementsFull;

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

    public function getCategoryId(): ?int
    {
        return $this->categoryId;
    }

    public function setCategoryId($categoryId): self
    {
        $this->categoryId = $categoryId;

        return $this;
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
            return $this->images[0]->getImageName();
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

    public function getSociete(): ?string
    {
        return $this->societe;
    }

    public function setSociete(string $societe): self
    {
        $this->societe = $societe;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getTelephoneAutre(): ?string
    {
        return $this->telephone_autre;
    }

    public function setTelephoneAutre(?string $telephone_autre): self
    {
        $this->telephone_autre = $telephone_autre;

        return $this;
    }

    public function getFax(): ?string
    {
        return $this->fax;
    }

    public function setFax(?string $fax): self
    {
        $this->fax = $fax;

        return $this;
    }

    public function getGsm(): ?string
    {
        return $this->gsm;
    }

    public function setGsm(?string $gsm): self
    {
        $this->gsm = $gsm;

        return $this;
    }

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setWebsite(?string $website): self
    {
        $this->website = $website;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getCentreville(): bool
    {
        return $this->centreville;
    }

    public function setCentreville(bool $centreville): self
    {
        $this->centreville = $centreville;

        return $this;
    }

    public function getMidi(): bool
    {
        return $this->midi;
    }

    public function setMidi(bool $midi): self
    {
        $this->midi = $midi;

        return $this;
    }

    public function getPmr(): bool
    {
        return $this->pmr;
    }

    public function setPmr(bool $pmr): self
    {
        $this->pmr = $pmr;

        return $this;
    }

    public function getFtlb(): ?int
    {
        return $this->ftlb;
    }

    public function setFtlb(?int $ftlb): self
    {
        $this->ftlb = $ftlb;

        return $this;
    }

    public function getUser(): ?string
    {
        return $this->user;
    }

    public function setUser(?string $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getAdresse(): ?Adresse
    {
        return $this->adresse;
    }

    public function setAdresse(?Adresse $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getNumeroTva(): ?string
    {
        return $this->numero_tva;
    }

    public function setNumeroTva(?string $numero_tva): self
    {
        $this->numero_tva = $numero_tva;

        return $this;
    }
}
