<?php


namespace AcMarche\Bottin\Hades\Entity;

interface OffreInterface
{
    public function getId(): int;

    public function getTitre(): string;

    public function getDescriptions(): array;

    public function getHoraire(): ?string;

    public function getLatitude(): ?float;

    public function getLongitude(): ?float;

    public function getRue(): ?string;

    public function getLocalite(): ?string;

    public function getCommune(): ?string;

    public function getCodePostal(): ?string;

    public function getNbEtoile(): ?int;

    public function getCivilite(): ?string;

    public function getContactNom(): ?string;

    public function getTelephone(): ?string;

    public function getFax(): ?string;

    public function getEmail(): ?string;

    public function getWebsite(): ?string;
}
