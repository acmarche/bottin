<?php


namespace AcMarche\Bottin\Entity\Traits;


use Symfony\Component\HttpFoundation\File\File;

trait LogoTrait
{

    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     *
     * @Vich\UploadableField(mapping="bottin_category_logo", fileNameProperty="logo")
     *
     * @var File
     */
    protected $logoFile;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @var string
     */
    protected $logo;

    /**
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $image
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
     * @return File
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
     * @var File
     */
    protected $logoBlancFile;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @var string
     */
    protected $logo_blanc;


    /**
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $image
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
     * @return File
     */
    public function getLogoBlancFile()
    {
        return $this->logoBlancFile;
    }
}
