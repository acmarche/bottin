<?php

namespace AcMarche\Bottin\Cbe\Entity;

use AcMarche\Bottin\Cbe\Repository\BranchRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=BranchRepository::class)
 * @ORM\Table(name="bce_branch", uniqueConstraints={
 *     @ORM\UniqueConstraint(name="code_idx", columns={"code", "language", "category"})})
 * @UniqueEntity(fields={"code", "language", "category"}, message="Déjà dans ce classement")
 */
class Branch
{
    /**
     * @ORM\Column(type="integer")
     */
    private int $id;
    /**
     * @ORM\Column(type="string", length=50, nullable=false)
     */
    public string $startDate; /**
     * @ORM\Column(type="string", length=50, nullable=false)
     */
    public string $enterpriseNumber;
}
