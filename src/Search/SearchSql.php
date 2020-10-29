<?php


namespace AcMarche\Bottin\Search;


use AcMarche\Bottin\Repository\FicheRepository;

class SearchSql implements SearchEngineInterface
{
    /**
     * @var FicheRepository
     */
    private $ficheRepository;

    public function __construct(FicheRepository $ficheRepository)
    {
        $this->ficheRepository = $ficheRepository;
    }

    function doSearch(string $keyword, ?string $localite): array
    {
        return $this->ficheRepository->searchByNameAndCity($keyword, $localite);
    }

    function doSearchAdvanced(string $keyword, ?string $localite): array
    {
        return $this->ficheRepository->searchByNameAndCity($keyword, $localite);
    }

    function renderResult(): array
    {
        // TODO: Implement renderResult() method.
    }

    function getFiches(array $fiches)
    {
        return $fiches;
    }
}
