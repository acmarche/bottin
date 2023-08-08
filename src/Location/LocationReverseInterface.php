<?php

namespace AcMarche\Bottin\Location;

interface LocationReverseInterface
{
    public function reverse($latitude, $longitude): array;

    public function getRoad(): ?string;

    public function getLocality(): ?string;

    public function getHouseNumber(): ?string;
}
