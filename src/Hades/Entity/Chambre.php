<?php


namespace AcMarche\Bottin\Hades\Entity;

class Chambre
{
    public function getId(): int
    {
        return (int)$this->hot_id[0];
    }

    public function getTitre(): string
    {
        var_dump($this->hot_titre);
        return $this->hot_titre;
    }


    public function getDescriptions(): array
    {
        // TODO: Implement getDescription() method.
    }
}
