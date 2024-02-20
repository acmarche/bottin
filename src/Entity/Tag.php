<?php

namespace AcMarche\Bottin\Entity;

use AcMarche\Bottin\Entity\Traits\IdTrait;
use AcMarche\Bottin\Tag\Repository\TagRepository;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\SluggableInterface;
use Knp\DoctrineBehaviors\Model\Sluggable\SluggableTrait;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'tag')]
#[ORM\Entity(repositoryClass: TagRepository::class)]
#[ORM\UniqueConstraint(columns: ['name'])]
#[UniqueEntity(fields: ['name'], message: 'Le nom doit être unique')]
class Tag implements \Stringable, SluggableInterface
{
    use IdTrait, SluggableTrait;

    #[Assert\NotBlank]
    #[ORM\Column(nullable: false, unique: true)]
    public ?string $name = null;

    #[ORM\Column( nullable: true)]
    public ?string $color = null;

    #[ORM\Column(nullable: true)]
    public ?string $icon = null;

    #[ORM\Column(unique: true, nullable: true)]
    protected $slug;

    /**
     * @var Fiche[]
     */
    public array $fiches = [];

    public function __toString(): string
    {
        return (string)$this->name;
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
