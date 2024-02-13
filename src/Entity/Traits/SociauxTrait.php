<?php

namespace AcMarche\Bottin\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait SociauxTrait
{
    #[ORM\Column(nullable: true)]
    public ?string $facebook = null;

    #[ORM\Column(nullable: true)]
    public ?string $twitter = null;

    #[ORM\Column(nullable: true)]
    public ?string $instagram = null;

    #[ORM\Column(nullable: true)]
    public ?string $tiktok = null;

    #[ORM\Column(nullable: true)]
    public ?string $youtube = null;

    #[ORM\Column(nullable: true)]
    public ?string $linkedin = null;
}
