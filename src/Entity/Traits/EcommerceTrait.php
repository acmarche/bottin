<?php

namespace AcMarche\Bottin\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait EcommerceTrait
{
    #[ORM\Column(type: 'boolean', nullable: false)]
    protected bool $click_collect = false;

    #[ORM\Column(type: 'boolean', nullable: false)]
    protected bool $ecommerce = false;

    public function isClickCollect(): bool
    {
        return $this->click_collect;
    }

    public function setClickCollect(bool $click_collect): void
    {
        $this->click_collect = $click_collect;
    }

    public function isEcommerce(): bool
    {
        return $this->ecommerce;
    }

    public function setEcommerce(bool $ecommerce): void
    {
        $this->ecommerce = $ecommerce;
    }
}
