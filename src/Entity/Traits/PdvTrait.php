<?php


namespace AcMarche\Bottin\Entity\Traits;

use AcMarche\Bottin\Entity\Pdv;
use Doctrine\ORM\Mapping as ORM;

trait PdvTrait
{
    /**
     * @ORM\ManyToOne(targetEntity="Pdv", inversedBy="fiches")
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL", nullable=true)
     */
    protected $pdv;

    public function getPdv(): ?Pdv
    {
        return $this->pdv;
    }

    public function setPdv(?Pdv $pdv): self
    {
        $this->pdv = $pdv;

        return $this;
    }

}
