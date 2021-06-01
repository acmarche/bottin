<?php


namespace AcMarche\Bottin\Entity\Traits;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

trait UuidTrait
{
    /**
     * @ORM\Column(type="uuid", unique=true, nullable=true)
     * @var ?string
     */
    private $uuid;

    /**
     * @return string|null
     */
    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    /**
     * @param string|null $uuid
     */
    public function setUuid(?string $uuid): void
    {
        $this->uuid = $uuid;
    }

    public function generateUuid(): string
    {
        return Uuid::v4();
    }
}
