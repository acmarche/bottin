<?php

namespace AcMarche\Bottin\Entity;

use AcMarche\Bottin\Entity\Traits\FicheFieldTrait;
use AcMarche\Bottin\Entity\Traits\IdTrait;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Stringable;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @Vich\Uploadable
 */
#[ORM\Entity(repositoryClass: 'AcMarche\Bottin\Repository\ImageRepository')]
#[ORM\Table(name: 'fiche_images')]
class FicheImage implements Stringable
{
    use FicheFieldTrait;
    use IdTrait;
    #[ORM\ManyToOne(targetEntity: 'Fiche', inversedBy: 'images')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    protected ?Fiche $fiche = null;
    #[ORM\Column(type: 'boolean', options: ['default' => 0])]
    protected bool $principale = false;
    /**
     * @Vich\UploadableField(mapping="bottin_fiche_image", fileNameProperty="imageName")
     *
     * note This is not a mapped field of entity metadata, just a simple property.
     */
    #[Assert\Image(maxSize: '7M')]
    protected ?File $image = null;
    #[ORM\Column(type: 'string', length: 255, name: 'image_name')]
    protected ?string $imageName = null;
    #[ORM\Column(type: 'string')]
    protected ?string $mime = null;
    /**
     * @var DateTime|DateTimeImmutable
     */
    #[ORM\Column(name: 'updated_at', type: 'datetime')]
    protected \DateTimeInterface $updatedAt;
    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the  update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     */
    public function setImage(?File $image = null): void
    {
        $this->image = $image;

        if (null !== $image) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new DateTime('now');
        }
    }
    public function getImage(): ?File
    {
        return $this->image;
    }
    /**
     * Pour ajouter plusieurs images d'un coup.
     */
    protected array $images;
    public function setImages(array $images): self
    {
        $this->images = $images;

        return $this;
    }
    public function getImages(): array
    {
        return $this->images;
    }
    public function __toString(): string
    {
        return $this->imageName;
    }
    public function __construct(Fiche $fiche)
    {
        $this->fiche = $fiche;
        $this->images = [];
        $this->updatedAt = new DateTime();
    }
    public function getPrincipale(): bool
    {
        return $this->principale;
    }
    public function setPrincipale(bool $principale): self
    {
        $this->principale = $principale;

        return $this;
    }
    public function getImageName(): ?string
    {
        return $this->imageName;
    }
    public function setImageName(?string $imageName): self
    {
        $this->imageName = $imageName;

        return $this;
    }
    public function getMime(): ?string
    {
        return $this->mime;
    }
    public function setMime(?string $mime): self
    {
        $this->mime = $mime;

        return $this;
    }
    /**
     * @return DateTime|DateTimeImmutable
     */
    public function getUpdatedAt(): \DateTimeInterface
    {
        return $this->updatedAt;
    }
    public function setUpdatedAt(DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
