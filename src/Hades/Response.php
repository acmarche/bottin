<?php

namespace AcMarche\Bottin\Hades;

use AcMarche\Bottin\Hades\Entity\Offre;

class Response
{
    /**
     * @var Offre[]
     */
    private $offres = [];

    /**
     * @return Offre[]
     */
    public function getOffres(): array
    {
        return $this->offres;
    }

    /**
     * @param Offre[] $offre
     */
    public function setOffres(array $offres): void
    {
        $this->offres = $offres;
    }

    public function addOffre(Offre $person): void
    {
        $this->offres[] = $person;
    }

}
