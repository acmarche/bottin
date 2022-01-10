<?php

namespace AcMarche\Bottin\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait InformationTrait
{
    #[ORM\Column(type: 'text', nullable: true)]
    protected ?string $comment1 = null;

    #[ORM\Column(type: 'text', nullable: true)]
    protected ?string $comment2 = null;

    #[ORM\Column(type: 'text', nullable: true)]
    protected ?string $comment3 = null;

    #[ORM\Column(type: 'text', nullable: true)]
    protected ?string $note = null;

    public function getComment1(): ?string
    {
        return $this->comment1;
    }

    public function setComment1(?string $comment1): self
    {
        $this->comment1 = $comment1;

        return $this;
    }

    public function getComment2(): ?string
    {
        return $this->comment2;
    }

    public function setComment2(?string $comment2): self
    {
        $this->comment2 = $comment2;

        return $this;
    }

    public function getComment3(): ?string
    {
        return $this->comment3;
    }

    public function setComment3(?string $comment3): self
    {
        $this->comment3 = $comment3;

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): self
    {
        $this->note = $note;

        return $this;
    }
}
