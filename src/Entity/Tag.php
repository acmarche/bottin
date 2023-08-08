<?php

namespace AcMarche\Bottin\Entity;

use AcMarche\Bottin\Tag\Repository\TagRepository;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model\Sluggable\SluggableTrait;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'tag')]
#[ORM\Entity(repositoryClass: TagRepository::class)]
class Tag implements \Stringable
{
    use SluggableTrait;

    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    public int $id;

    #[Assert\NotBlank]
    #[ORM\Column(type: 'string', nullable: false)]
    public ?string $name = null;

    #[ORM\Column(type: 'string', unique: true, nullable: true)]
    protected $slug;

    public $fiches = [];

    public function __toString(): string
    {
        return (string) $this->name;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getSluggableFields(): array
    {
        return ['name', 'id'];
    }

    public function shouldGenerateUniqueSlugs(): bool
    {
        return true;
    }
}
