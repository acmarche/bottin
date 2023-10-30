<?php

namespace AcMarche\Bottin\Entity;

use AcMarche\Bottin\Entity\Traits\FicheFieldTrait;
use AcMarche\Bottin\Entity\Traits\IdTrait;
use AcMarche\Bottin\Entity\Traits\UuidTrait;
use AcMarche\Bottin\Repository\TokenRepository;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;

#[ORM\Entity(repositoryClass: TokenRepository::class)]
#[ORM\Table(name: 'token')]
class Token implements TimestampableInterface
{
    use FicheFieldTrait;
    use IdTrait;
    use TimestampableTrait;
    use UuidTrait;

    #[ORM\OneToOne(targetEntity: Fiche::class, inversedBy: 'token')]
    #[ORM\JoinColumn(nullable: false)]
    public ?Fiche $fiche = null;

    #[ORM\Column(type: 'date', nullable: false)]
    public \DateTimeInterface $expireAt;

    #[ORM\Column(type: 'string', length: 50, nullable: false, unique: true)]
    public ?string $password = null;

    public function __construct(?Fiche $fiche)
    {
        $this->fiche = $fiche;
        $this->uuid = $this->generateUuid();
    }

}
