<?php

namespace AcMarche\Bottin\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait InformationTrait
{
    #[ORM\Column(type: 'text', nullable: true)]
    public ?string $comment1 = null;

    #[ORM\Column(type: 'text', nullable: true)]
    public ?string $comment2 = null;

    #[ORM\Column(type: 'text', nullable: true)]
    public ?string $comment3 = null;

    #[ORM\Column(type: 'text', nullable: true)]
    public ?string $note = null;
}
