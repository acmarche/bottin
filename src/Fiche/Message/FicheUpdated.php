<?php

namespace AcMarche\Bottin\Fiche\Message;

class FicheUpdated
{
    private $ficheId;
    /**
     * @var string|null
     */
    private $oldRue;

    public function __construct(int $ficheId, ?string $oldRue)
    {
        $this->ficheId = $ficheId;
        $this->oldRue = $oldRue;
    }

    public function getFicheId(): int
    {
        return $this->ficheId;
    }

    /**
     * @return string|null
     */
    public function getOldRue(): ?string
    {
        return $this->oldRue;
    }

}
