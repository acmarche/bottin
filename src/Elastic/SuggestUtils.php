<?php


namespace AcMarche\Bottin\Elastic;


class SuggestUtils
{

    public function getOptions(array $response)
    {
        $suggest = $this->getSuggest($response, 'societe_suggest');
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
