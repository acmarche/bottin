<?php

namespace AcMarche\Bottin\Entity\Traits;

use AcMarche\Bottin\Entity\FicheImage;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

trait ImageTrait
{
    /**
     * @var FicheImage[]|iterable|Collection
     */
    #[ORM\OneToMany(targetEntity: FicheImage::class, mappedBy: 'fiche', cascade: ['persist', 'remove'])]
    public iterable $images;

    public function addImage(FicheImage $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
            $image->fiche = $this;
        }

        return $this;
    }

    public function removeImage(FicheImage $image): self
    {
        if ($this->images->contains($image)) {
            $this->images->removeElement($image);
            // set the owning side to null (unless already changed)
            if ($image->fiche === $this) {
                $image->fiche = null;
            }
        }

        return $this;
    }
}
