<?php

namespace AcMarche\Bottin\Bce\Entity;

use AcMarche\Bottin\Bce\Repository\ActivityRepository;
use AcMarche\Bottin\Entity\Traits\IdTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=ActivityRepository::class)
 * @ORM\Table(name="bce_activity", uniqueConstraints={
 *     @ORM\UniqueConstraint(name="activity_idx", columns={"entity_number", "nace_code", "activity_group" })})
 * @UniqueEntity(fields={"naceCode", "entityNumber", "activityGroup"})
 */
class Activity
{
    use IdTrait;

    /**
     * @ORM\Column(type="string", length=50, nullable=false)
     */
    public string $entityNumber;
    /**
     * @ORM\Column(type="string", length=10, nullable=false)
     */
    public string $activityGroup;
    /**
     * @ORM\Column(type="smallint", length=5, nullable=false)
     */
    public int $naceVersion;
    /**
     * @ORM\Column(type="integer", length=20, nullable=false)
     */
    public int $naceCode;
    /**
     * @ORM\Column(type="string", length=50, nullable=false)
     */
    public string $classification;
    /**
     * @var array ['FR'=>'','NL'=>'']
     */
    public array $activityGroupDescription = [];
    public array $classificationDescription = [];
    public array $naceCodeDescription = [];

    public function __toString()
    {
        return $this->entityNumber;
    }
}
