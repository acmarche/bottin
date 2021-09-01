<?php

namespace AcMarche\Bottin\Elasticsearch;

use Elastica\ResultSet;

/**
 * Class SuggestUtils.
 */
class SuggestUtils
{
    public function getOptions(ResultSet $response)
    {
        $suggest = $this->getSuggest($response, 'societe_suggest');
        dump($suggest);

        return $suggest;
        if (!isset($suggest[0])) {
            return [];
        }

        return $suggest[0]['options'];
    }

    private function getSuggest(ResultSet $response, string $key): array
    {
        $suggests = $response->getSuggests();
        if (isset($suggests[$key])) {
            return $suggests[$key];
        }

        return [];
    }
}
