<?php

namespace AcMarche\Bottin\Fiche\Message;

class FicheUpdated
{
    private $ficheId;
    /**
     * @var string|null
     */
    private $oldAddress;

    public function __construct(int $ficheId, ?string $oldAddress)
    {
        $this->ficheId = $ficheId;
        $this->oldAddress = $oldAddress;
    }

    public function getFicheId(): int
    {
        return $this->ficheId;
    }

    /**
     * @return string|null
     */
    public function getOldAddress(): ?string
    {
        return $this->oldAddress;
    }

}
