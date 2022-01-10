<?php

namespace AcMarche\Bottin\Entity;

use AcMarche\Bottin\Doctrine\LogoTrait;
use AcMarche\Bottin\Entity\Traits\EnfantTrait;
use AcMarche\Bottin\Entity\Traits\IdTrait;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\SluggableInterface;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Contract\Entity\TreeNodeInterface;
use Knp\DoctrineBehaviors\Model\Sluggable\SluggableTrait;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Knp\DoctrineBehaviors\Model\Tree\TreeNodeTrait;
use Stringable;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @Vich\Uploadable
 * @ApiResource(
 *     normalizationContext={"groups"={"category:read"}},
 *     collectionOperations={"get"},
 *     itemOperations={"get"})
 * @ApiFilter(SearchFilter::class, properties={"name"="partial", "id"="exact"})
 */
#[ORM\Entity(repositoryClass: 'AcMarche\Bottin\Repository\CategoryRepository')]
#[ORM\Table(name: 'category')]
class Category implements SluggableInterface, TimestampableInterface, TreeNodeInterface, Stringable
{
    use LogoTrait;
    use TreeNodeTrait;
    use SluggableTrait;
    use TimestampableTrait;
    use EnfantTrait;
    use IdTrait;
    #[Assert\NotBlank]
    #[Groups(groups: ['category:read'])]
    #[ORM\Column(type: 'string', nullable: false)]
    protected ?string $name = null;
    #[ORM\ManyToOne(targetEntity: 'Category')]
    #[ORM\JoinColumn(name: 'parent_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private ?Category $parent = null;
    #[ORM\OneToMany(targetEntity: 'Classement', mappedBy: 'category', cascade: ['remove'])]
    protected iterable $classements;
    #[ORM\Column(type: 'boolean', options: ['default' => 0])]
    protected bool $mobile = false;
    #[Groups(groups: 'category:read')]
    #[ORM\Column(type: 'text', nullable: true)]
    protected ?string $description = null;
    /**
     * Utiliser pour afficher le classement.
     */
    protected array $path;
    private ArrayCollection $children;

    public function __construct()
    {
        $this->logo = null;
        $this->children = new ArrayCollection();
        $this->classements = new ArrayCollection();
    }

    public function getLabelHierarchical(): string
    {
        return str_repeat('-', $this->getNodeLevel() - 1).' '.$this->getName();
    }

    public function getPath(): array
    {
        return $this->path;
    }

    public function setPath(array $path): self
    {
        $this->path = $path;

        return $this;
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getMobile(): bool
    {
        return $this->mobile;
    }

    public function setMobile(bool $mobile): self
    {
        $this->mobile = $mobile;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(?string $logo): self
    {
        $this->logo = $logo;

        return $this;
    }

    public function getLogoBlanc(): ?string
    {
        return $this->logo_blanc;
    }

    public function setLogoBlanc(?string $logo_blanc): self
    {
        $this->logo_blanc = $logo_blanc;

        return $this;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Collection|Classement[]
     */
    public function getClassements(): iterable
    {
        return $this->classements;
    }

    public function addClassement(Classement $classement): self
    {
        if (!$this->classements->contains($classement)) {
            $this->classements[] = $classement;
            $classement->setCategory($this);
        }

        return $this;
    }

    public function removeClassement(Classement $classement): self
    {
        if ($this->classements->contains($classement)) {
            $this->classements->removeElement($classement);
            // set the owning side to null (unless already changed)
            if ($classement->getCategory() === $this) {
                $classement->setCategory(null);
            }
        }

        return $this;
    }
}
