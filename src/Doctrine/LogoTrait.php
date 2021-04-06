<?php


namespace AcMarche\Bottin\Doctrine;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

trait LogoTrait
{

    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     *
     * @Vich\UploadableField(mapping="bottin_category_logo", fileNameProperty="logo")
     *
     */
    protected $logoFile;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @var string
     */
    protected $logo;

    /**
     * @param File|null $image
     */
    public function setLogoFile(File $image = null)
    {
        $this->logoFile = $image;

        if ($image) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updated = new \DateTime('now');
        }
    }

    /**
     */
    public function getLogoFile()
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
     *
     */
    protected $logoBlancFile;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @var string
     */
    protected $logo_blanc;


    /**
     * @param File|null $image
     */
    public function setLogoBlancFile(File $image = null)
    {
        $this->logoBlancFile = $image;

        if ($image) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updated = new \DateTime('now');
        }
    }

    /**
     */
    public function getLogoBlancFile()
    {
        return $this->logoBlancFile;
    }
}
