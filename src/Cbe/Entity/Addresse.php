<?php

namespace AcMarche\Bottin\Cbe\Entity;

class Addresse
{
    public string $typeOfAddress;
    public string $zipcode;
    public string $municipalityNL;
    public string $municipalityFR;
    public string $streetNL;
    public string $streetFR;
    public string $houseNumber;
    /**
     * @var array fr => nl =>
     */
    public array $typeOfAddressDescription;
}
