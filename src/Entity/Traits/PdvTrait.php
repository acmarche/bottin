<?php


namespace AcMarche\Bottin\Entity\Traits;


trait PdvTrait
{

    /**
     * @ORM\ManyToOne(targetEntity="Pdv", inversedBy="fiches")
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     */
    protected $pdv;
}
