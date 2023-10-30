<?php

namespace AcMarche\Bottin\Entity;

use AcMarche\Bottin\Doctrine\LogoTrait;
use AcMarche\Bottin\Entity\Traits\IdTrait;
use AcMarche\Bottin\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\SluggableInterface;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Contract\Entity\TreeNodeInterface;
use Knp\DoctrineBehaviors\Model\Sluggable\SluggableTrait;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Knp\DoctrineBehaviors\Model\Tree\TreeNodeTrait;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[Vich\Uploadable]
#[ORM\Entity(repositoryClass: CategoryRepository::class)]
#[ORM\Table(name: 'category')]
class Category implements SluggableInterface, TimestampableInterface, TreeNodeInterface, \Stringable
{
    use IdTrait;
    use LogoTrait;
    use SluggableTrait;
    use TimestampableTrait;
    use TreeNodeTrait;

    #[Assert\NotBlank]
    #[Groups(groups: ['category:read'])]
    #[ORM\Column(type: 'string', nullable: false)]
    public ?string $name = null;

    #[ORM\ManyToOne(targetEntity: 'Category')]
    #[ORM\JoinColumn(name: 'parent_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    public ?Category $parent = null;

    #[ORM\OneToMany(targetEntity: 'Classement', mappedBy: 'category', cascade: ['remove'])]
    public iterable $classements;

    #[ORM\Column(type: 'boolean', options: ['default' => 0])]
    public bool $mobile = false;

    #[Groups(groups: 'category:read')]
    #[ORM\Column(type: 'text', nullable: true)]
    public ?string $description = null;

    /**
     * Utiliser pour afficher le classement.
     */
    public array $path;

    /**
     * @var ArrayCollection<Category>
     */
    public ArrayCollection $children;
    /**
     * @var Category[]
     */
    public array $enfants = [];

    public function __construct()
    {
        $this->logo = null;
        $this->children = new ArrayCollection();
        $this->classements = new ArrayCollection();
    }

    public function getLabelHierarchical(): string
    {
        return str_repeat('-', $this->getNodeLevel() - 1).' '.$this->name;
    }

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

    public function addClassement(Classement $classement): self
    {
        if (!$this->classements->contains($classement)) {
            $this->classements[] = $classement;
            $classement->category = $this;
        }

        return $this;
    }

    public function removeClassement(Classement $classement): self
    {
        if ($this->classements->contains($classement)) {
            $this->classements->removeElement($classement);
            // set the owning side to null (unless already changed)
            if ($classement->category === $this) {
                $classement->category = null;
            }
        }

        return $this;
    }
}
