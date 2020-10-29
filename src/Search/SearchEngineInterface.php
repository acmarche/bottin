<?php


namespace AcMarche\Bottin\Search;


interface SearchEngineInterface
{
    function doSearch(string $keyword, ?string $localite): array;
    function doSearchAdvanced(string $keyword, ?string $localite): array;
    function renderResult(): array;
    function getFiches(array $hits);
}
