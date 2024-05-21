<?php

namespace AcMarche\Bottin\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

trait SociauxTrait
{
    #[Assert\Url]
    #[ORM\Column(nullable: true)]
    public ?string $facebook = null;

    #[Assert\Url]
    #[ORM\Column(nullable: true)]
    public ?string $twitter = null;

    #[Assert\Url]
    #[ORM\Column(nullable: true)]
    public ?string $instagram = null;

    #[Assert\Url]
    #[ORM\Column(nullable: true)]
    public ?string $tiktok = null;

    #[Assert\Url]
    #[ORM\Column(nullable: true)]
    public ?string $youtube = null;

    #[Assert\Url]
    #[ORM\Column(nullable: true)]
    public ?string $linkedin = null;
}
