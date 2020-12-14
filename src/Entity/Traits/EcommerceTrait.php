<?php


namespace AcMarche\Bottin\Entity\Traits;


use Doctrine\ORM\Mapping as ORM;

trait EcommerceTrait
{
    /**
     * @var boolean
     * @ORM\Column(type="boolean", nullable=false)
     */
    protected $click_collect = false;

    /**
     * @var boolean
     * @ORM\Column(type="boolean", nullable=false)
     */
    protected $ecommerce = false;

    /**
     * @return bool
     */
    public function isClickCollect(): bool
    {
        return $this->click_collect;
    }

    /**
     * @param bool $click_collect
     */
    public function setClickCollect(bool $click_collect): void
    {
        $this->click_collect = $click_collect;
    }

    /**
     * @return bool
     */
    public function isEcommerce(): bool
    {
        return $this->ecommerce;
    }

    /**
     * @param bool $ecommerce
     */
    public function setEcommerce(bool $ecommerce): void
    {
        $this->ecommerce = $ecommerce;
    }


}