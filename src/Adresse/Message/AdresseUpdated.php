<?php

namespace AcMarche\Bottin\Adresse\Message;

class AdresseUpdated
{
    private $adresseId;
    /**
     * @var string|null
     */
    private $oldRue;

    public function __construct(int $adresseId, ?string $oldRue)
    {
        $this->adresseId = $adresseId;
        $this->oldRue = $oldRue;
    }

    public function getAdresseId(): int
    {
        return $this->adresseId;
    }

    /**
     * @return string|null
     */
    public function getOldRue(): ?string
    {
        return $this->oldRue;
    }

}
