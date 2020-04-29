<?php


namespace AcMarche\Bottin\Entity\Traits;


trait AdminTrait
{

    /**
     * ******************
     * CONTACT admin non-visible
     * *****************.
     */

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     */
    protected $admin_fonction;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     */
    protected $admin_civilite;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     */
    protected $admin_nom;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     */
    protected $admin_prenom;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     */
    protected $admin_telephone;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     */
    protected $admin_telephone_autre;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     */
    protected $admin_fax;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     */
    protected $admin_gsm;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     */
    protected $admin_email;

    public function getAdminFonction(): ?string
    {
        return $this->admin_fonction;
    }

    public function setAdminFonction(?string $admin_fonction): self
    {
        $this->admin_fonction = $admin_fonction;

        return $this;
    }

    public function getAdminCivilite(): ?string
    {
        return $this->admin_civilite;
    }

    public function setAdminCivilite(?string $admin_civilite): self
    {
        $this->admin_civilite = $admin_civilite;

        return $this;
    }

    public function getAdminNom(): ?string
    {
        return $this->admin_nom;
    }

    public function setAdminNom(?string $admin_nom): self
    {
        $this->admin_nom = $admin_nom;

        return $this;
    }

    public function getAdminPrenom(): ?string
    {
        return $this->admin_prenom;
    }

    public function setAdminPrenom(?string $admin_prenom): self
    {
        $this->admin_prenom = $admin_prenom;

        return $this;
    }

    public function getAdminTelephone(): ?string
    {
        return $this->admin_telephone;
    }

    public function setAdminTelephone(?string $admin_telephone): self
    {
        $this->admin_telephone = $admin_telephone;

        return $this;
    }

    public function getAdminTelephoneAutre(): ?string
    {
        return $this->admin_telephone_autre;
    }

    public function setAdminTelephoneAutre(?string $admin_telephone_autre): self
    {
        $this->admin_telephone_autre = $admin_telephone_autre;

        return $this;
    }

    public function getAdminFax(): ?string
    {
        return $this->admin_fax;
    }

    public function setAdminFax(?string $admin_fax): self
    {
        $this->admin_fax = $admin_fax;

        return $this;
    }

    public function getAdminGsm(): ?string
    {
        return $this->admin_gsm;
    }

    public function setAdminGsm(?string $admin_gsm): self
    {
        $this->admin_gsm = $admin_gsm;

        return $this;
    }

    public function getAdminEmail(): ?string
    {
        return $this->admin_email;
    }

    public function setAdminEmail(?string $admin_email): self
    {
        $this->admin_email = $admin_email;

        return $this;
    }
}
