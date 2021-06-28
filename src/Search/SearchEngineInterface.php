<?php

namespace AcMarche\Bottin\Search;


interface SearchEngineInterface
{
    /**
     * @param string $keyword
     * @param string|null $localite
     * @return iterable|\Elastica\ResultSet
     */
    public function doSearch(string $keyword, ?string $localite = null): iterable;

    public function doSearchAdvanced(string $keyword, ?string $localite = null): iterable;

    public function getFiches(iterable $hits): iterable;
}
