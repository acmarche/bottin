<?php

namespace AcMarche\Bottin\Bce\Entity;

use AcMarche\Bottin\Entity\Traits\IdTrait;
use Doctrine\ORM\Mapping as ORM;
use AcMarche\Bottin\Bce\Repository\CodeRepository;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=CodeRepository::class)
 * @ORM\Table(name="bce_code", uniqueConstraints={
 *     @ORM\UniqueConstraint(name="code_idx", columns={"code", "language", "category"})})
 * @UniqueEntity(fields={"code", "language", "category"}, message="Déjà dans ce classement")
 */
class Code
{
    use IdTrait;

    /**
     * @ORM\Column(type="string", length=50, nullable=false)
     */
    public string $category;
    /**
     * @ORM\Column(type="string", length=15, nullable=false)
     */
    public string $code;
    /**
     * @ORM\Column(type="string", length=5, nullable=false)
     */
    public string $language;
    /**
     * @ORM\Column(type="string", length=250, nullable=false)
     */
    public string $description;

    public function __toString()
    {
        return $this->code;
    }
}
