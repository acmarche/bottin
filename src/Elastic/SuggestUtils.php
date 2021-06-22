<?php


namespace AcMarche\Bottin\Elastic;

/**
 * Class SuggestUtils
 * @package AcMarche\Bottin\Elastic
 *
 * https://www.elastic.co/guide/en/elasticsearch/reference/current/search-suggesters.html
 */
class SuggestUtils
{
    public function getOptions(array $response)
    {
        $suggest = $this->getSuggest($response, 'societe_suggest');
        dump();
        if (!isset($suggest[0])) {
            return [];
        }
        return $suggest[0]['options'];
    }

    private function getSuggest(array $response, string $key): array
    {
        if (isset($response['suggest'][$key])) {
            return $response['suggest'][$key];
        }
        return [];
    }
}
