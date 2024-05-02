<?php

namespace AcMarche\Bottin\Entity\Traits;

use AcMarche\Bottin\Entity\Tag;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

trait TagTrait
{
    #[ORM\ManyToMany(targetEntity: Tag::class, cascade: ['persist'])]
    public array|Collection $tags = [];

    public function addTag(Tag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
        }

        return $this;
    }

    public function removeTag(Tag $tag): self
    {
        $this->tags->removeElement($tag);

        return $this;
    }
}
