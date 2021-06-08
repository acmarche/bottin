<?php


namespace AcMarche\Bottin\Entity\Traits;


use AcMarche\Bottin\Entity\Token;
use Doctrine\ORM\Mapping as ORM;

trait TokenTrait
{
    /**
     * @ORM\OneToOne(targetEntity="AcMarche\Bottin\Entity\Token", mappedBy="fiche")
     */
    private ?Token $token;

    /**
     * @return \AcMarche\Bottin\Entity\Token|null
     */
    public function getToken(): ?Token
    {
        return $this->token;
    }

    /**
     * @param \AcMarche\Bottin\Entity\Token|null $token
     */
    public function setToken(?Token $token): void
    {
        $this->token = $token;
    }
}
