<?php


namespace AcMarche\Bottin\Entity\Traits;


trait EnfantTrait
{
    /**
     * @var array
     */
    private $enfants = [];

    /**
     * @return array
     */
    public function getEnfants(): array
    {
        return $this->enfants;
    }

    /**
     * @param array $enfants
     */
    public function setEnfants(array $enfants): void
    {
        $this->enfants = $enfants;
    }

}
