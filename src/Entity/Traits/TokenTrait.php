<?php


namespace AcMarche\Bottin\Entity\Traits;


trait TokenTrait
{
    /**
     * @var string|null
     * @ORM\Column(type="guid", nullable=true)
     */
    protected $token;

    /**
     * @return string|null
     */
    public function getToken(): ?string
    {
        return $this->token;
    }

    /**
     * @param string|null $token
     */
    public function setToken(?string $token): void
    {
        //todo sf 5.1 uuid
        $this->token = $token;
    }


}
