<?php

namespace AcMarche\Bottin\Bce\Entity;

use AcMarche\Bottin\Bce\Repository\MetaRepository;
use AcMarche\Bottin\Entity\Traits\IdTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=MetaRepository::class)
 * @ORM\Table(name="bce_meta", uniqueConstraints={
 *     @ORM\UniqueConstraint(name="meta_idx", columns={"variable"})})
 * @UniqueEntity(fields={"variable"}, message="Déjà dans ce classement")
 */
class Meta
{
    use IdTrait;

    /**
     * @ORM\Column(type="string", length=150, nullable=false, unique=true)
     */
    public string $variable;
    /**
     * @ORM\Column(type="string", length=150, nullable=false)
     */
    public string $value;
}
