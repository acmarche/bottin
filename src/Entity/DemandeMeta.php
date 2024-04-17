<?php

namespace AcMarche\Bottin\Entity;

use AcMarche\Bottin\Entity\Traits\IdTrait;
use AcMarche\Bottin\Repository\DemandeMetaRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: DemandeMetaRepository::class)]
#[ORM\Table(name: 'demande_metas')]
class DemandeMeta
{
    use IdTrait;

    #[ORM\ManyToOne(targetEntity: Demande::class, inversedBy: 'metas')]
    #[ORM\JoinColumn(nullable: false)]
    public Demande $demande;

    #[Assert\NotBlank()]
    #[ORM\Column(type: 'string', nullable: false)]
    public ?string $champ;

    #[ORM\Column(type: 'string', nullable: true)]
    public ?string $value;

    public function __construct(
        Demande $demande,
        ?string $champ,
        ?string $value
    ) {
        $this->demande = $demande;
        $this->champ = $champ;
        $this->value = $value;
    }

}
