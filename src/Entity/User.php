<?php

namespace AcMarche\Bottin\Entity;

use AcMarche\Bottin\Entity\Traits\IdTrait;
use AcMarche\Bottin\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface, \Stringable
{
    use IdTrait;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    public ?string $username = null;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    public ?string $email = null;

    #[ORM\Column(type: 'string', length: 180, nullable: false)]
    public ?string $nom = null;

    #[ORM\Column(type: 'string', length: 180, nullable: true)]
    public ?string $prenom = null;

    #[ORM\Column(type: 'json')]
    public array $roles = [];

    #[ORM\Column(type: 'string')]
    public string $password;

    public ?string $plainPassword = null;

    public function getUserIdentifier(): string
    {
        return $this->username;
    }

    public function __toString(): string
    {
        return $this->getUserIdentifier();
    }

    public function addRole(string $role): void
    {
        if (!\in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }
    }

    public function removeRole(string $role): void
    {
        if (\in_array($role, $this->roles, true)) {
            $index = array_search($role, $this->roles, true);
            unset($this->roles[$index]);
        }
    }

    public function hasRole(string $role): bool
    {
        return \in_array($role, $this->getRoles(), true);
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
}
