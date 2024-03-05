<?php

namespace AcMarche\Bottin\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

trait LogoTrait
{
    #[Vich\UploadableField(mapping: 'bottin_category_logo', fileNameProperty: 'logo')]
    public ?File $logoFile = null;

    #[ORM\Column(type: 'string', nullable: true)]
    public ?string $logo = null;

    public function setLogoFile(File $file = null)
    {
        $this->logoFile = $file;
        if ($file instanceof File) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->setUpdatedAt(new \DateTime('now'));
        }
    }

    #[Vich\UploadableField(mapping: 'bottin_category_logo', fileNameProperty: 'logo_blanc')]
    public ?File $logoBlancFile = null;

    #[ORM\Column(type: 'string', nullable: true)]
    public ?string $logo_blanc = null;

    public function setLogoBlancFile(File $file = null)
    {
        $this->logoBlancFile = $file;
        if ($file instanceof File) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->setUpdatedAt(new \DateTime('now'));
        }
    }

    #[Vich\UploadableField(mapping: 'bottin_category_icon', fileNameProperty: 'icon')]
    public ?File $iconFile = null;

    #[ORM\Column(type: 'string', nullable: true)]
    public ?string $icon = null;

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
