<?php

namespace AcMarche\Bottin\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass="AcMarche\Bottin\Repository\ImageRepository")
 * @ORM\Table(name="fiche_images")
 * @Vich\Uploadable
 */
class FicheImage
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var Fiche|null
     * @ORM\ManyToOne(targetEntity="Fiche", inversedBy="images")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE"))
     */
    protected $fiche;

    /**
     * @var bool
     * @ORM\Column(type="boolean", options={"default": 0})
     */
    protected $principale = false;

    /**
     * @Vich\UploadableField(mapping="bottin_fiche_image", fileNameProperty="imageName")
     *
     * note This is not a mapped field of entity metadata, just a simple property.
     * @Assert\Image(
     *     maxSize="7M"
     * )
     *
     * @var File
     */
    protected $image;

    /**
     * @ORM\Column(type="string", length=255, name="image_name")
     *
     * @var string|null
     */
    protected $imageName;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    protected $mime;

    /**
     * @ORM\Column(name="updated_at", type="datetime")
     *
     * @var \DateTime
     */
    protected $updatedAt;

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the  update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $image
     */
    public function setImage(File $image = null)
    {
        $this->image = $image;

        if ($image) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTime('now');
        }
    }

    /**
     * @return File
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Pour ajouter plusieurs images d'un coup.
     *
     * @var array
     */
    protected $images;

    public function setImages(array $images)
    {
        $this->images = $images;

        return $this;
    }

    public function getImages()
    {
        return $this->images;
    }

    public function __toString()
    {
        return $this->imageName;
    }

    public function __construct(Fiche $fiche)
    {
        $this->fiche = $fiche;
        $this->images = [];
        $this->updatedAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrincipale(): ?bool
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

    public function setMime(string $mime): self
    {
        $this->mime = $mime;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getFiche(): ?Fiche
    {
        return $this->fiche;
    }

    public function setFiche(?Fiche $fiche): self
    {
        $this->fiche = $fiche;

        return $this;
    }
}
