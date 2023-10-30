<?php

namespace AcMarche\Bottin\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait EcommerceTrait
{
    #[ORM\Column(type: 'boolean', nullable: false)]
    public bool $click_collect = false;

    #[ORM\Column(type: 'boolean', nullable: false)]
    public bool $ecommerce = false;

    public function isClickCollect(): bool
    {
        return $this->click_collect;
    }

    public function setClickCollect(bool $click_collect): void
    {
        $this->click_collect = $click_collect;
    }

}
