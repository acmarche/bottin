<?php

namespace AcMarche\Bottin\Cbe\Entity;

class Establishments
{
    public string $establishmentNumber;
    public string $startDate;
    /**
     * @var array|Addresse[]
     */
    public array $addresses;
    /**
     * @var array|Contact[]
     */
    public array $contacts;
    /**
     * @var array|Activity[]
     */
    public array $activities;
    /**
     * @var array|Denomination[]
     */
    public array $denominations;
}
