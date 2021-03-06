<?php

namespace AcMarche\Bottin\Entity;

use AcMarche\Bottin\Entity\Traits\IdTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AcMarche\Bottin\Repository\DemandeMetaRepository")
 * @ORM\Table(name="demande_metas")
 */
class DemandeMeta
{
    use IdTrait;

    /**
     * @ORM\ManyToOne(targetEntity="AcMarche\Bottin\Entity\Demande", inversedBy="metas")
     */
    protected Demande $demande;

    /**
     * @ORM\Column(type="string", nullable=false)
     * @Assert\NotBlank
     */
    protected ?string $champ;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected ?string $value;

    public function __construct(Demande $demande, string $champ, ?string $value)
    {
        $this->demande = $demande;
        $this->champ = $champ;
        $this->value = $value;
    }

    public function getChamp(): ?string
    {
        return $this->champ;
    }

    public function setChamp(string $champ): self
    {
        $this->champ = $champ;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(?string $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getDemande(): Demande
    {
        return $this->demande;
    }

    public function setDemande(Demande $demande): self
    {
        $this->demande = $demande;

        return $this;
    }
}
