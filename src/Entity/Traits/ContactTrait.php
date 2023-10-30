<?php

namespace AcMarche\Bottin\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait ContactTrait
{
    #[ORM\Column(type: 'string', nullable: true)]
    public ?string $fonction = null;

    #[ORM\Column(type: 'string', nullable: true)]
    public ?string $civilite = null;

    #[ORM\Column(type: 'string', nullable: true)]
    public ?string $nom = null;

    #[ORM\Column(type: 'string', nullable: true)]
    public ?string $prenom = null;

    #[ORM\Column(type: 'string', nullable: true)]
    public ?string $contact_rue = null;

    #[ORM\Column(type: 'string', nullable: true)]
    public ?string $contact_num = null;

    #[ORM\Column(type: 'string', nullable: true)]
    public ?string $contact_cp = null;

    #[ORM\Column(type: 'string', nullable: true)]
    public ?string $contact_localite = null;

    #[ORM\Column(type: 'string', nullable: true)]
    public ?string $contact_telephone = null;

    #[ORM\Column(type: 'string', nullable: true)]
    public ?string $contact_telephone_autre = null;

    #[ORM\Column(type: 'string', nullable: true)]
    public ?string $contact_fax = null;

    #[ORM\Column(type: 'string', nullable: true)]
    public ?string $contact_gsm = null;

    #[ORM\Column(type: 'string', nullable: true)]
    public ?string $contact_email = null;

    public function getContactRue(): ?string
    {
        return $this->contact_rue;
    }

    public function getContactNum(): ?string
    {
        return $this->contact_num;
    }

    public function getContactCp(): ?string
    {
        return $this->contact_cp;
    }

    public function getContactLocalite(): ?string
    {
        return $this->contact_localite;
    }

    public function getContactTelephone(): ?string
    {
        return $this->contact_telephone;
    }

    public function getContactTelephoneAutre(): ?string
    {
        return $this->contact_telephone_autre;
    }

    public function getContactFax(): ?string
    {
        return $this->contact_fax;
    }

    public function getContactGsm(): ?string
    {
        return $this->contact_gsm;
    }

    public function getContactEmail(): ?string
    {
        return $this->contact_email;
    }
}
