<?php

namespace AcMarche\Bottin\Search;

interface SearchEngineInterface
{
    public function doSearch(string $keyword, ?string $localite): array;

    public function doSearchAdvanced(string $keyword, ?string $localite): array;

    public function renderResult(): array;

    public function getFiches(array $hits);
}
