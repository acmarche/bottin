<?php

namespace AcMarche\Bottin\Search;

use Elastica\ResultSet;
interface SearchEngineInterface
{
    /**
     * @return iterable|ResultSet
     */
    public function doSearch(string $keyword, ?string $localite = null): iterable;

    /**
     * @return iterable|ResultSet
     */
    public function doSearchAdvanced(string $keyword, ?string $localite = null): iterable;

    public function getFiches(iterable $hits): iterable;
}
