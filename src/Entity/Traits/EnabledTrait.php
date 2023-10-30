<?php

namespace AcMarche\Bottin\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait EnabledTrait
{
    #[ORM\Column(type: 'boolean')]
    public bool $enabled = true;
}
