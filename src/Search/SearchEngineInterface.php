<?php

namespace AcMarche\Bottin\Search;

interface SearchEngineInterface
{
    public function doSearch(string $keyword, ?string $localite = null): array;

    public function doSearchAdvanced(string $keyword, ?string $localite = null): array;

    public function renderResult(): array;

    public function getFiches(array $hits);
}
