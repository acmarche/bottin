<?php


namespace AcMarche\Bottin\Entity\Traits;

use AcMarche\Bottin\Entity\FicheImage;
use Doctrine\Common\Collections\Collection;

trait ImageTrait
{
    /**
     * @var FicheImage[]|iterable
     * @ORM\OneToMany(targetEntity="FicheImage", mappedBy="fiche", cascade={"persist", "remove"})
     */
    protected $images;

    /**
     * @return Collection|FicheImage[]
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(FicheImage $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
            $image->setFiche($this);
        }

        return $this;
    }

    public function removeImage(FicheImage $image): self
    {
        if ($this->images->contains($image)) {
            $this->images->removeElement($image);
            // set the owning side to null (unless already changed)
            if ($image->getFiche() === $this) {
                $image->setFiche(null);
            }
        }

        return $this;
    }

    /**
     * Pour elastic
     * @param array $images
     */
    public function setImages(array $images) {
        $this->images = $images;
    }

}
