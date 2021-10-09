<?php

namespace AcMarche\Bottin\Cbe\Entity;

class Entreprise
{   //"EnterpriseNumber","Status","JuridicalSituation","TypeOfEnterprise","JuridicalForm","StartDate"
    public string $enterpriseNumber;
    public string $status;
    public string $juridicalSituation;
    public int $typeOfEnterprise;
    public int $juridicalForm;
    public string $startDate;
    /**
     * @var array|Activity[]
     */
    public array $activities;
    /**
     * @var array|Establishments[]
     */
    public array $establishments;
    /**
     * @var array|Denomination[]
     */
    public array $denominations;
    /**
     * @var array|Contact[]
     */
    public array $contacts;
    /**
     * @var array|Addresse[]
     */
    public array $addresses;
    /**
     * @var array ['fr'=>'']
     */
    public array $statusDescription;
    public array $juridicalSituationDescription;
    public array $typeOfEnterpriseDescription;
    public array $juridicalFormDescription;
}
