<?php

namespace AcMarche\Bottin\Entity;

use AcMarche\Bottin\Entity\Traits\FicheFieldTrait;
use AcMarche\Bottin\Entity\Traits\IdTrait;
use AcMarche\Bottin\Repository\DocumentRepository;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[Vich\Uploadable]
#[ORM\Entity(repositoryClass: DocumentRepository::class)]
class Document implements TimestampableInterface, \Stringable
{
    use FicheFieldTrait;
    use IdTrait;
    use TimestampableTrait;
    #[Assert\NotBlank]
    #[ORM\Column(type: 'string', nullable: false)]
    public string $name;

    #[ORM\Column(type: 'text', nullable: true)]
    public ?string $description = null;

    #[ORM\ManyToOne(targetEntity: Fiche::class, inversedBy: 'documents')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    public ?Fiche $fiche = null;

    #[Vich\UploadableField(mapping: 'bottin_fiche_document', fileNameProperty: 'fileName', size: 'fileSize')]
    #[Assert\File(maxSize: '16384k', mimeTypes: ['application/pdf', 'application/x-pdf'], mimeTypesMessage: 'Uniquement des PDF')]
    public ?File $file = null;

    #[ORM\Column(type: 'string')]
    public ?string $fileName = null;

    #[ORM\Column(type: 'integer')]
    public ?int $fileSize = null;

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     */
    public function setDocFile(File $file = null): void
    {
        $this->file = $file;

        if ($file instanceof File) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function __construct(Fiche $fiche)
    {
        $this->fiche = $fiche;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
