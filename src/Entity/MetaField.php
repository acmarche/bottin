<?php

namespace AcMarche\Bottin\Entity;

use AcMarche\Bottin\Entity\Traits\IdTrait;
use AcMarche\Bottin\Meta\Repository\MetaFieldRepository;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\SluggableInterface;
use Knp\DoctrineBehaviors\Model\Sluggable\SluggableTrait;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MetaFieldRepository::class)]
#[ORM\Table(name: 'meta_field')]
class MetaField implements SluggableInterface
{
    use IdTrait, SluggableTrait;

    #[Assert\NotBlank()]
    #[ORM\Column(nullable: false)]
    public ?string $name;
    #[ORM\Column(nullable: true)]
    public ?string $description;

    public function __toString(): string
    {
        return $this->name;
    }

    public function getSluggableFields(): array
    {
        return ['name'];
    }

    public function shouldGenerateUniqueSlugs(): bool
    {
        return true;
    }
}