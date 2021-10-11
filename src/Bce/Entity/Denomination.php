<?php

namespace AcMarche\Bottin\Bce\Entity;

use AcMarche\Bottin\Entity\Traits\IdTrait;
use Doctrine\ORM\Mapping as ORM;
use AcMarche\Bottin\Bce\Repository\DenominationRepository;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=DenominationRepository::class)
 * @ORM\Table(name="bce_denomination", uniqueConstraints={
 *     @ORM\UniqueConstraint(name="denomination_idx", columns={"entity_number", "type_of_denomination"})})
 * @UniqueEntity(fields={"entityNumber", "typeOfDenomination"}, message="Déjà dans ce classement")
 */
class Denomination
{
    use IdTrait;

    /**
     * @ORM\Column(type="string", length=50, nullable=false)
     */
    public string $entityNumber;
    /**
     * @ORM\Column(type="smallint", length=10, nullable=false)
     */
    public int $language;
    /**
     * @ORM\Column(type="smallint", length=10, nullable=false)
     */
    public int $typeOfDenomination;
    /**
     * @ORM\Column(type="string", length=150, nullable=false)
     */
    public string $denomination;
}
