<?php

namespace AcMarche\Bottin\Cbe\Entity;

use AcMarche\Bottin\Entity\Traits\IdTrait;
use Doctrine\ORM\Mapping as ORM;
use AcMarche\Bottin\Cbe\Repository\CodeRepository;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=CodeRepository::class)
 * @ORM\Table(name="bce_contact", uniqueConstraints={
 *     @ORM\UniqueConstraint(name="code_idx", columns={"code", "language", "category"})})
 * @UniqueEntity(fields={"code", "language", "category"}, message="Déjà dans ce classement")
 */
class Contact
{
    use IdTrait;

    /**
     * @ORM\Column(type="string", length=50, nullable=false)
     */
    public string $entityNumber;
    /**
     * @ORM\Column(type="string", length=50, nullable=false)
     */
    public string $entityContact;
    /**
     * @ORM\Column(type="string", length=50, nullable=false)
     */
    public string $contactType;
    /**
     * @ORM\Column(type="string", length=50, nullable=false)
     */
    public string $value;

}
