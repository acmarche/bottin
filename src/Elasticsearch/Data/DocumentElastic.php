<?php

namespace AcMarche\Bottin\Elasticsearch\Data;

class DocumentElastic
{
    public string $id;
    public string $numero;
    public string $description;
    public ?string $categorie;
    public string $expediteur;
    public array $destinataires;
    public array $services;
    public string $date_courrier;
    public string $url;
    public array $original;
    public array $copie;
    public bool $recommande;
}
