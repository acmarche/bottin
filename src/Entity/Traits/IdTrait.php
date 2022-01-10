<?php

namespace AcMarche\Bottin\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

trait IdTrait
{
    #[Groups(groups: ['category:read'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = 0;

    public function getId(): int
    {
        return $this->id;
    }
}
