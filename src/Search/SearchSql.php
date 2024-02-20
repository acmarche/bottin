<?php

namespace AcMarche\Bottin\Search;

use AcMarche\Bottin\Repository\FicheRepository;

class SearchSql implements SearchEngineInterface
{
    public function __construct(private readonly FicheRepository $ficheRepository)
    {
    }

    public function doSearch(string $keyword, string $localite = null): iterable
    {
        return $this->ficheRepository->searchByNameAndCity($keyword, $localite);
    }

    public function doSearchAdvanced(string $keyword, string $localite = null, array $filters = []): iterable
    {
        return $this->ficheRepository->searchByNameAndCity($keyword, $localite);
    }

    public function getFiches(iterable $fiches): iterable
    {
        return $fiches;
    }
}
