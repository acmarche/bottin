<?php

namespace AcMarche\Bottin\Entity;

use AcMarche\Bottin\Entity\Traits\AdminTrait;
use AcMarche\Bottin\Entity\Traits\CapTrait;
use AcMarche\Bottin\Entity\Traits\ClassementTrait;
use AcMarche\Bottin\Entity\Traits\ContactTrait;
use AcMarche\Bottin\Entity\Traits\DemandeTrait;
use AcMarche\Bottin\Entity\Traits\DocumentsTrait;
use AcMarche\Bottin\Entity\Traits\EnabledTrait;
use AcMarche\Bottin\Entity\Traits\HoraireTrait;
use AcMarche\Bottin\Entity\Traits\ImageTrait;
use AcMarche\Bottin\Entity\Traits\InformationTrait;
use AcMarche\Bottin\Entity\Traits\PdvTrait;
use AcMarche\Bottin\Entity\Traits\SociauxTrait;
use AcMarche\Bottin\Entity\Traits\TokenTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\SluggableInterface;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Sluggable\SluggableTrait;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AcMarche\Bottin\Repository\FicheRepository")
 * @ORM\Table(name="fiche")
 */
class Fiche implements SluggableInterface, TimestampableInterface
{
    use SluggableTrait,
        TimestampableTrait,
        AdminTrait,
        ClassementTrait,
        ContactTrait,
        DemandeTrait,
        HoraireTrait,
        ImageTrait,
        DocumentsTrait,
        InformationTrait,
        SociauxTrait,
        PdvTrait,
        TokenTrait,
        EnabledTrait,
        CapTrait;

    /**
     * @var int|null
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=false)
     * @Assert\NotBlank
     *
     */
    protected $societe;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     */
    protected $rue;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     */
    protected $numero;

    /**
     * @var int|null
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $cp;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     */
    protected $localite;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     */
    protected $telephone;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     */
    protected $telephone_autre;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     */
    protected $fax;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     */
    protected $gsm;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     */
    protected $website;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     */
    protected $email;

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
     * @var bool
     * @ORM\Column(type="boolean", options={"default": 0})
     */
    protected $centreville = false;

    /**
     * @var bool
     * @ORM\Column(type="boolean", options={"default": 0})
     */
    protected $midi = false;

    /**
     * @var bool
     * @ORM\Column(type="boolean", options={"default": 0})
     */
    protected $pmr = false;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $ftlb;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     */
    protected $user;

    /**
     * Utiliser lors de l'ajout d'un classement.
     *
     * @var int|null
     */
    protected $categoryId;

    public function __construct()
    {
        $this->classements = new ArrayCollection();
        $this->horaires = new ArrayCollection();
        $this->images = new ArrayCollection();
        $this->documents = new ArrayCollection();
        $this->demandes = new ArrayCollection();
    }

    public function getCategoryId()
    {
        return $this->categoryId;
    }

    public function setCategoryId($categoryId)
    {
        $this->categoryId = $categoryId;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getAdresse(bool $withNumero = true): ?string
    {
        if ($this->getRue()) {
            $adresse = '';
            if ($this->getNumero() && $withNumero) {
                $adresse = $this->getNumero() . ' ';
            }
            return $adresse . $this->getRue() . ' ' . $this->getCp() . ' ' . $this->getLocalite() . ' Belgium';
        } else {
            return 'Rue du Commerce Marche-en-Famenne Beligum';
        }
    }

    public function getSluggableFields(): array
    {
        return ['societe'];
    }

    public function shouldGenerateUniqueSlugs(): bool
    {
        return true;
    }

    public function __toString()
    {
        return $this->societe;
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getRue(): ?string
    {
        return $this->rue;
    }

    public function setRue(?string $rue): self
    {
        $this->rue = $rue;

        return $this;
    }

    public function getNumero(): ?string
    {
        return $this->numero;
    }

    public function setNumero(?string $numero): self
    {
        $this->numero = $numero;

        return $this;
    }

    public function getCp(): ?int
    {
        return $this->cp;
    }

    public function setCp(?int $cp): self
    {
        $this->cp = $cp;

        return $this;
    }

    public function getLocalite(): ?string
    {
        return $this->localite;
    }

    public function setLocalite(?string $localite): self
    {
        $this->localite = $localite;

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

    public function getLongitude(): ?string
    {
        return $this->longitude;
    }

    public function setLongitude(?string $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getLatitude(): ?string
    {
        return $this->latitude;
    }

    public function setLatitude(?string $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getCentreville(): ?bool
    {
        return $this->centreville;
    }

    public function setCentreville(bool $centreville): self
    {
        $this->centreville = $centreville;

        return $this;
    }

    public function getMidi(): ?bool
    {
        return $this->midi;
    }

    public function setMidi(bool $midi): self
    {
        $this->midi = $midi;

        return $this;
    }

    public function getPmr(): ?bool
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

}
