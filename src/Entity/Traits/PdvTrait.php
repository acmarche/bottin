<?php

namespace AcMarche\Bottin\Entity\Traits;

use AcMarche\Bottin\Entity\Pdv;
use Doctrine\ORM\Mapping as ORM;

trait PdvTrait
{
    /**
     * @ORM\ManyToOne(targetEntity="AcMarche\Bottin\Entity\Pdv", inversedBy="fiches")
     * @ORM\JoinColumn(nullable=true)
     */
    protected ?Pdv $pdv = null;

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
