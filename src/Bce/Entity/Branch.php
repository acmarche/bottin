<?php

namespace AcMarche\Bottin\Bce\Entity;

use AcMarche\Bottin\Bce\Repository\BranchRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=BranchRepository::class)
 * @ORM\Table(name="bce_branch", uniqueConstraints={
 *     @ORM\UniqueConstraint(name="branch_idx", columns={"id"})})
 * @UniqueEntity(fields={"id"}, message="Déjà dans ce classement")
 */
class Branch
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    public int $idx;

    /**
     * @ORM\Column(type="integer", unique=true)
     */
    public int $id;

    /**
     * @ORM\Column(type="string", length=50, nullable=false)
     */
    public string $startDate;
    /**
     * @ORM\Column(type="string", length=50, nullable=false, unique=true)
     */
    public string $enterpriseNumber;
}
