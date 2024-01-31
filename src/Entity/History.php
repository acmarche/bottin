<?php

namespace AcMarche\Bottin\Entity;

use AcMarche\Bottin\Entity\Traits\FicheFieldTrait;
use AcMarche\Bottin\Entity\Traits\IdTrait;
use AcMarche\Bottin\Repository\HistoryRepository;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;

#[ORM\Entity(repositoryClass: HistoryRepository::class)]
#[ORM\Table(name: 'history')]
class History implements TimestampableInterface
{
    use FicheFieldTrait;
    use IdTrait;
    use TimestampableTrait;

    #[ORM\ManyToOne(targetEntity: Fiche::class, inversedBy: 'histories')]
    #[ORM\JoinColumn(nullable: true)]
    public ?Fiche $fiche = null;

    #[ORM\Column(type: 'string', nullable: true)]
    public ?string $old_value = null;

    #[ORM\Column(type: 'string', nullable: true)]
    public ?string $new_value = null;

    #[ORM\Column(type: 'string', nullable: true)]
    public ?string $made_by;

    #[ORM\Column(type: 'string', nullable: true)]
    public ?string $property;

    public function __construct(
        ?Fiche $fiche,
        ?string $made_by,
        ?string $property,
        ?string $old_value,
        ?string $new_value
    ) {
        $this->fiche = $fiche;
        if ($old_value) {
            $this->old_value = substr($old_value, 0, 250);
        } else {
            $this->old_value = null;
        }
        if ($new_value) {
            $this->new_value = substr($new_value, 0, 250);
        } else {
            $this->new_value = null;
        }
        $this->made_by = $made_by;
        $this->property = $property;
    }
}
