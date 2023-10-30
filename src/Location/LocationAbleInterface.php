<?php

namespace AcMarche\Bottin\Location;

use AcMarche\Bottin\Entity\Adresse;

interface LocationAbleInterface
{
    public function getRue(): ?string;

    public function getNumero(): ?string;

    public function getCp(): ?int;

    public function getLocalite();

    public function getLongitude(): ?string;

    public function getLatitude(): ?string;

    public function getAdresse(): ?Adresse;
}
