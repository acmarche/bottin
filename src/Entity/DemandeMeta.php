<?php

namespace AcMarche\Bottin\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AcMarche\Bottin\Repository\DemandeMetaRepository")
 * @ORM\Table(name="demande_metas")
 */
class DemandeMeta
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var Demande
     * @ORM\ManyToOne(targetEntity="AcMarche\Bottin\Entity\Demande", inversedBy="metas")
     */
    protected $demande;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=false)
     * @Assert\NotBlank
     */
    protected $champ;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     */
    protected $value;

    public function __construct(Demande $demande, string $champ, ?string $value)
    {
        $this->demande = $demande;
        $this->champ = $champ;
        $this->value = $value;
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDemande(): ?Demande
    {
        return $this->demande;
    }

    public function setDemande(?Demande $demande): self
    {
        $this->demande = $demande;

        return $this;
    }
}
