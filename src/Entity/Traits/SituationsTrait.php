<?php


namespace AcMarche\Bottin\Entity\Traits;

use AcMarche\Bottin\Entity\Situation;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

trait SituationsTrait
{
    /**
     * @ORM\ManyToMany(targetEntity="AcMarche\Bottin\Entity\Situation", inversedBy="fiches")
     *
     */
    protected $situations;

    /**
     * @return Collection|Situation[]
     */
    public function getSituations(): Collection
    {
        return $this->situations;
    }

    public function addSituation(Situation $situation): self
    {
        if (!$this->situations->contains($situation)) {
            $this->situations[] = $situation;
        }

        return $this;
    }

    public function removeSituation(Situation $situation): self
    {
        if ($this->situations->contains($situation)) {
            $this->situations->removeElement($situation);
        }

        return $this;
    }

}
