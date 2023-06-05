<?php

namespace AcMarche\Bottin\Entity;

use AcMarche\Bottin\Entity\Traits\FicheFieldTrait;
use AcMarche\Bottin\Entity\Traits\IdTrait;
use AcMarche\Bottin\Entity\Traits\UuidTrait;
use AcMarche\Bottin\Repository\TokenRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;

#[ORM\Entity(repositoryClass: TokenRepository::class)]
#[ORM\Table(name: 'token')]
class Token implements TimestampableInterface
{
    use IdTrait;
    use FicheFieldTrait;
    use UuidTrait;
    use TimestampableTrait;

    #[ORM\OneToOne(targetEntity: Fiche::class, inversedBy: 'token')]
    #[ORM\JoinColumn(nullable: false)]
    protected ?Fiche $fiche = null;
    #[ORM\Column(type: 'date', nullable: false)]
    protected DateTimeInterface $expireAt;
    #[ORM\Column(type: 'string', length: 50, nullable: false, unique: true)]
    protected ?string $password = null;

    public function __construct(?Fiche $fiche)
    {
        $this->fiche = $fiche;
        $this->uuid = $this->generateUuid();
    }

    public function getExpireAt(): DateTimeInterface
    {
        return $this->expireAt;
    }

    public function setExpireAt(DateTimeInterface $expireAt): self
    {
        $this->expireAt = $expireAt;

        return $this;
    }

    public function generatePassword(): void
    {
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }
}
