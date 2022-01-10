<?php

namespace AcMarche\Bottin\Search;

interface SearchEngineInterface
{
    public function doSearch(string $keyword, ?string $localite = null): iterable;

    public function doSearchAdvanced(string $keyword, ?string $localite = null): iterable;

    public function getFiches(iterable $hits): iterable;
}
