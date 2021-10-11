<?php

namespace AcMarche\Bottin\Cbe\Entity;

use AcMarche\Bottin\Entity\Traits\IdTrait;
use Doctrine\ORM\Mapping as ORM;
use AcMarche\Bottin\Cbe\Repository\CodeRepository;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=CodeRepository::class)
 * @ORM\Table(name="bce_denomination", uniqueConstraints={
 *     @ORM\UniqueConstraint(name="code_idx", columns={"code", "language", "category"})})
 * @UniqueEntity(fields={"code", "language", "category"}, message="Déjà dans ce classement")
 */
class Denomination
{
    use IdTrait;

    /**
     * @ORM\Column(type="string", length=50, nullable=false)
     */
    public string $entityNumber;
    /**
     * @ORM\Column(type="string", length=50, nullable=false)
     */
    public string $language;
    /**
     * @ORM\Column(type="string", length=50, nullable=false)
     */
    public string $typeOfDenomination;
    /**
     * @ORM\Column(type="string", length=50, nullable=false)
     */
    public string $denomination;
    /**
     * @ORM\Column(type="string", length=50, nullable=false)
     */
    public array $languageDescription;
    /**
     * @ORM\Column(type="string", length=50, nullable=false)
     */
    public array $typeOfDenominationDescription;
}
