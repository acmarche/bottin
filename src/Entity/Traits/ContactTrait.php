<?php


namespace AcMarche\Bottin\Entity\Traits;


trait ContactTrait
{
    /**
     * ******************
     * CONTACT visible
     * *****************.
     */

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     */
    protected $fonction;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     */
    protected $civilite;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     */
    protected $nom;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     */
    protected $prenom;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     */
    protected $contact_rue;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     */
    protected $contact_num;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     */
    protected $contact_cp;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     */
    protected $contact_localite;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     */
    protected $contact_telephone;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     */
    protected $contact_telephone_autre;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     */
    protected $contact_fax;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     */
    protected $contact_gsm;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     */
    protected $contact_email;

    public function getFonction(): ?string
    {
        return $this->fonction;
    }

    public function setFonction(?string $fonction): self
    {
        $this->fonction = $fonction;

        return $this;
    }

    public function getCivilite(): ?string
    {
        return $this->civilite;
    }

    public function setCivilite(?string $civilite): self
    {
        $this->civilite = $civilite;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(?string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getContactRue(): ?string
    {
        return $this->contact_rue;
    }

    public function setContactRue(?string $contact_rue): self
    {
        $this->contact_rue = $contact_rue;

        return $this;
    }

    public function getContactNum(): ?string
    {
        return $this->contact_num;
    }

    public function setContactNum(?string $contact_num): self
    {
        $this->contact_num = $contact_num;

        return $this;
    }

    public function getContactCp(): ?string
    {
        return $this->contact_cp;
    }

    public function setContactCp(?string $contact_cp): self
    {
        $this->contact_cp = $contact_cp;

        return $this;
    }

    public function getContactLocalite(): ?string
    {
        return $this->contact_localite;
    }

    public function setContactLocalite(?string $contact_localite): self
    {
        $this->contact_localite = $contact_localite;

        return $this;
    }

    public function getContactTelephone(): ?string
    {
        return $this->contact_telephone;
    }

    public function setContactTelephone(?string $contact_telephone): self
    {
        $this->contact_telephone = $contact_telephone;

        return $this;
    }

    public function getContactTelephoneAutre(): ?string
    {
        return $this->contact_telephone_autre;
    }

    public function setContactTelephoneAutre(?string $contact_telephone_autre): self
    {
        $this->contact_telephone_autre = $contact_telephone_autre;

        return $this;
    }

    public function getContactFax(): ?string
    {
        return $this->contact_fax;
    }

    public function setContactFax(?string $contact_fax): self
    {
        $this->contact_fax = $contact_fax;

        return $this;
    }

    public function getContactGsm(): ?string
    {
        return $this->contact_gsm;
    }

    public function setContactGsm(?string $contact_gsm): self
    {
        $this->contact_gsm = $contact_gsm;

        return $this;
    }

    public function getContactEmail(): ?string
    {
        return $this->contact_email;
    }

    public function setContactEmail(?string $contact_email): self
    {
        $this->contact_email = $contact_email;

        return $this;
    }


}
