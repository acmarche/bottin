<?php

namespace AcMarche\Bottin\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AcMarche\Bottin\Repository\HoraireRepository")
 * @ORM\Table(name="horaire")
 */
class Horaire
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var Fiche|null
     * @ORM\ManyToOne(targetEntity="Fiche", inversedBy="horaires")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $fiche;

    /**
     * @var int|null
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $day;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     */
    protected $media_path;

    /**
     * @var bool
     * @ORM\Column(type="boolean", options={"default": 0})
     */
    protected $is_open_at_lunch = false;

    /**
     * @var bool
     * @ORM\Column(type="boolean", options={"default": 0})
     */
    protected $is_rdv = false;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    protected $morning_start;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    protected $morning_end;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    protected $noon_start;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    protected $noon_end;

    /**
     * @var bool
     * @ORM\Column(type="boolean", options={"default": 0})
     */
    protected $is_closed = false;

    public function isEmpty()
    {
        if (!$this->getIsClosed() && !$this->getMorningStart() && !$this->getMorningEnd()
            && !$this->getNoonStart() && !$this->getNoonEnd() && !$this->getIsRdv()) {
            return true;
        }

        return false;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDay(): ?int
    {
        return $this->day;
    }

    public function setDay(?int $day): self
    {
        $this->day = $day;

        return $this;
    }

    public function getMediaPath(): ?string
    {
        return $this->media_path;
    }

    public function setMediaPath(?string $media_path): self
    {
        $this->media_path = $media_path;

        return $this;
    }

    public function getIsOpenAtLunch(): ?bool
    {
        return $this->is_open_at_lunch;
    }

    public function setIsOpenAtLunch(bool $is_open_at_lunch): self
    {
        $this->is_open_at_lunch = $is_open_at_lunch;

        return $this;
    }

    public function getIsRdv(): ?bool
    {
        return $this->is_rdv;
    }

    public function setIsRdv(bool $is_rdv): self
    {
        $this->is_rdv = $is_rdv;

        return $this;
    }

    public function getMorningStart(): ?\DateTimeInterface
    {
        return $this->morning_start;
    }

    public function setMorningStart(?\DateTimeInterface $morning_start): self
    {
        $this->morning_start = $morning_start;

        return $this;
    }

    public function getMorningEnd(): ?\DateTimeInterface
    {
        return $this->morning_end;
    }

    public function setMorningEnd(?\DateTimeInterface $morning_end): self
    {
        $this->morning_end = $morning_end;

        return $this;
    }

    public function getNoonStart(): ?\DateTimeInterface
    {
        return $this->noon_start;
    }

    public function setNoonStart(?\DateTimeInterface $noon_start): self
    {
        $this->noon_start = $noon_start;

        return $this;
    }

    public function getNoonEnd(): ?\DateTimeInterface
    {
        return $this->noon_end;
    }

    public function setNoonEnd(?\DateTimeInterface $noon_end): self
    {
        $this->noon_end = $noon_end;

        return $this;
    }

    public function setModifyDate(\DateTimeInterface $modify_date): self
    {
        $this->modify_date = $modify_date;

        return $this;
    }

    public function getIsClosed(): ?bool
    {
        return $this->is_closed;
    }

    public function setIsClosed(bool $is_closed): self
    {
        $this->is_closed = $is_closed;

        return $this;
    }

    public function getFiche(): ?Fiche
    {
        return $this->fiche;
    }

    public function setFiche(?Fiche $fiche): self
    {
        $this->fiche = $fiche;

        return $this;
    }
}
