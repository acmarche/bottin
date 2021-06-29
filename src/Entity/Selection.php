<?php

namespace AcMarche\Bottin\Entity;

use AcMarche\Bottin\Entity\Traits\IdTrait;
use AcMarche\Bottin\Repository\SelectionRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SelectionRepository::class)
 */
class Selection
{
   use IdTrait;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class)
     */
    private Category $category;

    /**
     * @ORM\Column(type="string", length=120)
     */
    private string $user;

    public function __construct(Category $category, string $user)
    {
        $this->category = $category;
        $this->user = $user;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getUser(): ?string
    {
        return $this->user;
    }

    public function setUser(string $user): self
    {
        $this->user = $user;

        return $this;
    }
}
