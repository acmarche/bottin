<?php

namespace AcMarche\Bottin\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait SociauxTrait
{
    #[ORM\Column(type: 'string', nullable: true)]
    public ?string $facebook = null;

    #[ORM\Column(type: 'string', nullable: true)]
    public ?string $twitter = null;

    #[ORM\Column(type: 'string', nullable: true)]
    public ?string $instagram = null;
}
