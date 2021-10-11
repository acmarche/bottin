<?php

namespace AcMarche\Bottin\Cbe\Entity;

use AcMarche\Bottin\Cbe\Repository\AddresseRepository;
use AcMarche\Bottin\Entity\Traits\IdTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=AddresseRepository::class)
 * @ORM\Table(name="bce_addresse", uniqueConstraints={
 *     @ORM\UniqueConstraint(name="code_idx", columns={"entityNumber", "zipcode"})})
 * @UniqueEntity(fields={"entityNumber", "zipcode"}, message="Déjà dans ce classement")
 */
class Addresse
{
    use IdTrait;

    /**
     * @ORM\Column(type="string", length=50, nullable=false)
     */
    public string $entityNumber;
    /**
     * @ORM\Column(type="string", length=50, nullable=false)
     */
    public string $typeOfAddress;
    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    public ?string $countryNL = null;
    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    public ?string $countryFR = null;
    /**
     * @ORM\Column(type="int", length=6, nullable=false)
     */
    public int $zipcode;
    /**
     * @ORM\Column(type="string", length=50, nullable=false)
     */
    public string $municipalityNL;
    /**
     * @ORM\Column(type="string", length=50, nullable=false)
     */
    public string $municipalityFR;
    /**
     * @ORM\Column(type="string", length=50, nullable=false)
     */
    public string $streetNL;
    /**
     * @ORM\Column(type="string", length=50, nullable=false)
     */
    public string $streetFR;
    /**
     * @ORM\Column(type="string", length=50, nullable=false)
     */
    public string $houseNumber;
    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    public ?string $box = null;
    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    public ?string $extraAddressInfo = null;
    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    public ?string $dateStrikingOff = null;
    /**
     * @var array fr => nl =>
     */
    public array $typeOfAddressDescription;
}
