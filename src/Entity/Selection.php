<?php

namespace AcMarche\Bottin\Entity;

use AcMarche\Bottin\Entity\Traits\IdTrait;
use AcMarche\Bottin\Repository\SelectionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SelectionRepository::class)]
class Selection
{
    use IdTrait;

    #[ORM\ManyToOne(targetEntity: Category::class)]
    public Category $category;
    #[ORM\Column(type: 'string', length: 120)]
    public string $user;

    public function __construct(Category $category, string $user)
    {
        $this->category = $category;
        $this->user = $user;
    }

}
