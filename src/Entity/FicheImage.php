<?php

namespace AcMarche\Bottin\Entity;

use AcMarche\Bottin\Entity\Traits\FicheFieldTrait;
use AcMarche\Bottin\Entity\Traits\IdTrait;
use AcMarche\Bottin\Repository\ImageRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[Vich\Uploadable]
#[ORM\Entity(repositoryClass: ImageRepository::class)]
#[ORM\Table(name: 'fiche_images')]
class FicheImage implements \Stringable
{
    use FicheFieldTrait;
    use IdTrait;

    #[ORM\ManyToOne(targetEntity: 'Fiche', inversedBy: 'images')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    public ?Fiche $fiche = null;

    #[ORM\Column(type: 'boolean', options: ['default' => 0])]
    public bool $principale = false;

    #[Vich\UploadableField(mapping: 'bottin_fiche_image', fileNameProperty: 'imageName')]
    #[Assert\Image(maxSize: '7M')]
    public ?File $image = null;

    #[ORM\Column(type: 'string', length: 255, name: 'image_name')]
    public ?string $imageName = null;

    #[ORM\Column(type: 'string')]
    public ?string $mime = null;

    #[ORM\Column(name: 'updated_at', type: 'datetime')]
    public \DateTimeInterface $updatedAt;

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the  update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     */
    public function setImage(File $image = null): void
    {
        $this->image = $image;

        if ($image instanceof File) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTime('now');
        }
    }

    /**
     * Pour ajouter plusieurs images d'un coup.
     */
    public array $images = [];

    public function __toString(): string
    {
        return $this->imageName;
    }

    public function __construct(Fiche $fiche)
    {
        $this->fiche = $fiche;
        $this->updatedAt = new \DateTime();
    }

}
