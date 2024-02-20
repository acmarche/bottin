<?php

namespace AcMarche\Bottin\Search;

use Meilisearch\Search\SearchResult;

interface SearchEngineInterface
{
    public function doSearch(string $keyword, string $localite = null): iterable|SearchResult;

    public function doSearchAdvanced(string $keyword, string $localite = null, array $filters = []): iterable;

}
