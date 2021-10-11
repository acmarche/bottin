<?php

namespace AcMarche\Bottin\Cbe\Entity;

use AcMarche\Bottin\Entity\Traits\IdTrait;
use Doctrine\ORM\Mapping as ORM;
use AcMarche\Bottin\Cbe\Repository\CodeRepository;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=CodeRepository::class)
 * @ORM\Table(name="bce_entreprise", uniqueConstraints={
 *     @ORM\UniqueConstraint(name="code_idx", columns={"code", "language", "category"})})
 * @UniqueEntity(fields={"code", "language", "category"}, message="Déjà dans ce classement")
 */
class Entreprise
{
    use IdTrait;

    //"EnterpriseNumber","Status","JuridicalSituation","TypeOfEnterprise","JuridicalForm","StartDate"

    /**
     * @ORM\Column(type="string", length=50, nullable=false)
     */
    public string $enterpriseNumber;
    /**
     * @ORM\Column(type="string", length=50, nullable=false)
     */
    public string $status;
    /**
     * @ORM\Column(type="string", length=50, nullable=false)
     */
    public string $juridicalSituation;
    /**
     * @ORM\Column(type="string", length=50, nullable=false)
     */
    public int $typeOfEnterprise;
    /**
     * @ORM\Column(type="string", length=50, nullable=false)
     */
    public int $juridicalForm;
    /**
     * @ORM\Column(type="string", length=50, nullable=false)
     */
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
