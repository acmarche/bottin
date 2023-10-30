<?php

namespace AcMarche\Bottin\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait AdminTrait
{
    #[ORM\Column(type: 'string', nullable: true)]
    public ?string $admin_fonction = null;

    #[ORM\Column(type: 'string', nullable: true)]
    public ?string $admin_civilite = null;

    #[ORM\Column(type: 'string', nullable: true)]
    public ?string $admin_nom = null;

    #[ORM\Column(type: 'string', nullable: true)]
    public ?string $admin_prenom = null;

    #[ORM\Column(type: 'string', nullable: true)]
    public ?string $admin_telephone = null;

    #[ORM\Column(type: 'string', nullable: true)]
    public ?string $admin_telephone_autre = null;

    #[ORM\Column(type: 'string', nullable: true)]
    public ?string $admin_fax = null;

    #[ORM\Column(type: 'string', nullable: true)]
    public ?string $admin_gsm = null;

    #[ORM\Column(type: 'string', nullable: true)]
    public ?string $admin_email = null;

    public function getAdminFonction(): ?string
    {
        return $this->admin_fonction;
    }

    public function getAdminCivilite(): ?string
    {
        return $this->admin_civilite;
    }

    public function getAdminNom(): ?string
    {
        return $this->admin_nom;
    }

    public function getAdminPrenom(): ?string
    {
        return $this->admin_prenom;
    }

    public function getAdminTelephone(): ?string
    {
        return $this->admin_telephone;
    }

    public function getAdminTelephoneAutre(): ?string
    {
        return $this->admin_telephone_autre;
    }

    public function getAdminFax(): ?string
    {
        return $this->admin_fax;
    }

    public function getAdminGsm(): ?string
    {
        return $this->admin_gsm;
    }

    public function getAdminEmail(): ?string
    {
        return $this->admin_email;
    }

}
