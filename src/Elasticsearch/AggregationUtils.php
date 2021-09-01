<?php

namespace AcMarche\Bottin\Elasticsearch;

use Elastica\ResultSet;

/**
 * todo utiliser $this->search->getAggregations()
 * Class AggregationUtils.
 */
class AggregationUtils
{
    public function getAggregations(ResultSet $response, string $name): ResultSet
    {
        $aggregations = $response->getAggregation($name);
        dump($aggregations);
        if (!isset($aggregations['buckets'])) {
            return [];
        }

        return $aggregations['buckets'];
    }

    public function getLocalites(ResultSet $response): array
    {
        return $this->getAggregations($response, 'localites');
    }

    public function countPmr(ResultSet $response): int
    {
        return $this->countTrue($response, 'pmr');
    }

    public function countMidi(ResultSet $response): int
    {
        return $this->countTrue($response, 'midi');
    }

    public function countCentreVille(ResultSet $response): int
    {
        return $this->countTrue($response, 'centre_ville');
    }

    private function countTrue(ResultSet $response, string $key): int
    {
        $agg = $response->getAggregation($key);
        if (!isset($agg['buckets'])) {
            return 0;
        }
        foreach ($agg['buckets'] as $data) {
            if ('true' == $data['key']) {
                return $data['doc_count'];
            }
        }

        return 0;
    }
}
