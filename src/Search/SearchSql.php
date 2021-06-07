<?php

namespace AcMarche\Bottin\Search;

use AcMarche\Bottin\Repository\FicheRepository;

class SearchSql implements SearchEngineInterface
{
    private FicheRepository $ficheRepository;

    public function __construct(FicheRepository $ficheRepository)
    {
        $this->ficheRepository = $ficheRepository;
    }

    public function doSearch(string $keyword, ?string $localite): array
    {
        return $this->ficheRepository->searchByNameAndCity($keyword, $localite);
    }

    public function doSearchAdvanced(string $keyword, ?string $localite): array
    {
        return $this->ficheRepository->searchByNameAndCity($keyword, $localite);
    }

    public function renderResult(): array
    {
        // TODO: Implement renderResult() method.
    }

    public function getFiches(array $fiches): array
    {
        return $fiches;
    }
}
