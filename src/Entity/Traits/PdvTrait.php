<?php

namespace AcMarche\Bottin\Entity\Traits;

use AcMarche\Bottin\Entity\Pdv;
use Doctrine\ORM\Mapping as ORM;

trait PdvTrait
{
    #[ORM\ManyToOne(targetEntity: Pdv::class, inversedBy: 'fiches')]
    #[ORM\JoinColumn(nullable: true)]
    public ?Pdv $pdv = null;
}
