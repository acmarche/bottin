<?php

namespace AcMarche\Bottin\Bce\Entity;

use AcMarche\Bottin\Bce\Repository\EnterpriseRepository;
use AcMarche\Bottin\Entity\Traits\IdTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=EnterpriseRepository::class)
 * @ORM\Table(name="bce_entreprise", uniqueConstraints={
 *     @ORM\UniqueConstraint(name="enterprise_idx", columns={"enterprise_number"})})
 * @UniqueEntity(fields={"enterpriseNumber"}, message="Déjà dans ce classement")
 */
class Enterprise
{
    use IdTrait;

    /**
     * @ORM\Column(type="string", length=50, nullable=false, unique=true)
     */
    public string $enterpriseNumber;
    /**
     * @ORM\Column(type="string", length=50, nullable=false)
     */
    public string $status;
    /**
     * @ORM\Column(type="smallint", length=5, nullable=false)
     */
    public int $juridicalSituation;
    /**
     * @ORM\Column(type="smallint", length=5, nullable=false)
     */
    public int $typeOfEnterprise;
    /**
     * @ORM\Column(type="smallint", length=5, nullable=false)
     */
    public int $juridicalForm;
    /**
     * @ORM\Column(type="string", length=20, nullable=false)
     */
    public string $startDate;
    /**
     * @var array|Activity[]
     */
    public array $activities;
    /**
     * @var array|Establishment[]
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
