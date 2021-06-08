<?php

namespace AcMarche\Bottin\Entity;

use AcMarche\Bottin\Entity\Traits\FicheFieldTrait;
use AcMarche\Bottin\Entity\Traits\IdTrait;
use AcMarche\Bottin\Entity\Traits\UuidTrait;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;

/**
 * Class Token.
 *
 * @ORM\Entity(repositoryClass="AcMarche\Bottin\Repository\TokenRepository")
 * @ORM\Table(name="token")
 */
class Token implements TimestampableInterface
{
    use IdTrait;
    use FicheFieldTrait;
    use UuidTrait;
    use TimestampableTrait;

    /**
     * @ORM\OneToOne(targetEntity="AcMarche\Bottin\Entity\Fiche", inversedBy="token")
     * @ORM\JoinColumn(nullable=false)
     */
    protected ?Fiche $fiche;

    public function __construct(Fiche $fiche)
    {
        $this->fiche = $fiche;
       // $this->uuid = $this->generateUuid();
    }
}
