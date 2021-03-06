<?php

namespace AcMarche\Bottin\Doctrine;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

trait LogoTrait
{
    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     *
     * @Vich\UploadableField(mapping="bottin_category_logo", fileNameProperty="logo")
     */
    protected ?File $logoFile = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected ?string $logo = null;

    public function setLogoFile(File $file = null)
    {
        $this->logoFile = $file;
        if ($file !== null) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updated = new DateTime('now');
        }
    }

    public function getLogoFile(): ?File
    {
        return $this->logoFile;
    }

    /**
     * LOGO BLANC.
     */
    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     *
     * @Vich\UploadableField(mapping="bottin_category_logo", fileNameProperty="logo_blanc")
     */
    protected ?File $logoBlancFile = null;
    /**
     * @ORM\Column(type="string", nullable=true)
     *
     */
    protected ?string $logo_blanc = null;

    /**
     * @param File|null $file
     */
    public function setLogoBlancFile(File $file = null)
    {
        $this->logoBlancFile = $file;
        if ($file !== null) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updated = new DateTime('now');
        }
    }

    public function getLogoBlancFile(): ?File
    {
        return $this->logoBlancFile;
    }
}
