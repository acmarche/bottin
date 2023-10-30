<?php

namespace AcMarche\Bottin\Entity\Traits;

use AcMarche\Bottin\Entity\Token;
use Doctrine\ORM\Mapping as ORM;

trait TokenTrait
{
    #[ORM\OneToOne(targetEntity: Token::class, mappedBy: 'fiche', cascade: ['remove'])]
    public ?Token $token = null;
}
