<?php

namespace AcMarche\Bottin\Entity;

use AcMarche\Bottin\Entity\Traits\IdTrait;
use AcMarche\Bottin\Tag\Repository\TagRepository;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\SluggableInterface;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Sluggable\SluggableTrait;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Table(name: 'tag')]
#[ORM\Entity(repositoryClass: TagRepository::class)]
#[ORM\UniqueConstraint(columns: ['name'])]
#[UniqueEntity(fields: ['name'], message: 'Le nom doit être unique')]
#[Vich\Uploadable]
class Tag implements \Stringable, SluggableInterface, TimestampableInterface
{
    use IdTrait, SluggableTrait, TimestampableTrait;

    #[Assert\NotBlank]
    #[ORM\Column(nullable: false, unique: true)]
    public ?string $name = null;

    #[Assert\NotBlank]
    #[ORM\Column(nullable:  true)]
    public ?string $groupe = null;

    #[ORM\Column(nullable: true)]
    public ?string $color = null;

    #[Vich\UploadableField(mapping: 'bottin_tag_icon', fileNameProperty: 'icon')]
    public ?File $iconFile = null;

    #[ORM\Column(type: 'string', nullable: true)]
    public ?string $icon = null;

    #[ORM\Column(nullable: false)]
    public bool $private = false;

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

    public function setIconFile(File $file = null)
    {
        $this->iconFile = $file;
        if ($file instanceof File) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->setUpdatedAt(new \DateTime('now'));
        }
    }
}
