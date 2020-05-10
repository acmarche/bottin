<?php


namespace AcMarche\Bottin\Hades\Entity;


interface OffreInterface
{
    public function getId(): int;
    public function getTitre(): string;
    public function getDescriptions(): array ;
}
