<?php

namespace AcMarche\Bottin\Hades\Entity;

class Chambre
{
    public $hot_id;
    public $hot_titre;

    public function getId(): int
    {
        return (int) $this->hot_id[0];
    }

    public function getTitre(): string
    {
        var_dump($this->hot_titre);

        return $this->hot_titre;
    }

    public function getDescriptions(): void
    {
        // TODO: Implement getDescription() method.
    }
}
