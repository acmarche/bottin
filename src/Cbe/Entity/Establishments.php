<?php

namespace AcMarche\Bottin\Cbe\Entity;

use AcMarche\Bottin\Entity\Traits\IdTrait;
use Doctrine\ORM\Mapping as ORM;
use AcMarche\Bottin\Cbe\Repository\CodeRepository;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=CodeRepository::class)
 * @ORM\Table(name="bce_establishment", uniqueConstraints={
 *     @ORM\UniqueConstraint(name="code_idx", columns={"code", "language", "category"})})
 * @UniqueEntity(fields={"code", "language", "category"}, message="Déjà dans ce classement")
 */
class Establishments
{
    use IdTrait;

    /**
     * @ORM\Column(type="string", length=50, nullable=false)
     */
    public string $establishmentNumber;
    /**
     * @ORM\Column(type="string", length=50, nullable=false)
     */
    public string $enterpriseNumber;
    /**
     * @ORM\Column(type="string", length=50, nullable=false)
     */
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
