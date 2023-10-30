<?php

namespace AcMarche\Bottin\Entity;

use AcMarche\Bottin\Entity\Traits\IdTrait;
use AcMarche\Bottin\Repository\HoraireRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HoraireRepository::class)]
#[ORM\Table(name: 'horaire')]
class Horaire
{
    use IdTrait;

    #[ORM\ManyToOne(targetEntity: 'Fiche', inversedBy: 'horaires')]
    #[ORM\JoinColumn(nullable: false)]
    public ?Fiche $fiche = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    public ?int $day = null;

    #[ORM\Column(type: 'string', nullable: true)]
    public ?string $media_path = null;

    #[ORM\Column(type: 'boolean', options: ['default' => 0])]
    public bool $is_open_at_lunch = false;

    #[ORM\Column(type: 'boolean', options: ['default' => 0])]
    public bool $is_rdv = false;

    #[ORM\Column(type: 'time', nullable: true)]
    public ?\DateTimeInterface $morning_start = null;

    #[ORM\Column(type: 'time', nullable: true)]
    public ?\DateTimeInterface $morning_end = null;

    #[ORM\Column(type: 'time', nullable: true)]
    public ?\DateTimeInterface $noon_start = null;

    #[ORM\Column(type: 'time', nullable: true)]
    public ?\DateTimeInterface $noon_end = null;

    #[ORM\Column(type: 'boolean', options: ['default' => 0])]
    public bool $is_closed = false;

    public function isEmpty(): bool
    {
        return !$this->is_closed && !$this->morning_start && !$this->morning_end
            && !$this->noon_start && !$this->noon_end && !$this->is_rdv;
    }

    public function getMediaPath(): ?string
    {
        return $this->media_path;
    }

    public function isIsOpenAtLunch(): bool
    {
        return $this->is_open_at_lunch;
    }

    public function isIsRdv(): bool
    {
        return $this->is_rdv;
    }

    public function getMorningStart(): ?\DateTimeInterface
    {
        return $this->morning_start;
    }

    public function getMorningEnd(): ?\DateTimeInterface
    {
        return $this->morning_end;
    }

    public function getNoonStart(): ?\DateTimeInterface
    {
        return $this->noon_start;
    }

    public function getNoonEnd(): ?\DateTimeInterface
    {
        return $this->noon_end;
    }

    public function isIsClosed(): bool
    {
        return $this->is_closed;
    }



}
