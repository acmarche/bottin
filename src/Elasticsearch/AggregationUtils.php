<?php

namespace AcMarche\Bottin\Elasticsearch;

/**
 * todo utiliser $this->search->getAggregations()
 * Class AggregationUtils.
 */
class AggregationUtils
{
    public function getLocalites(array $response): array
    {
        $agg = $this->getAggreations($response, 'localites');
        if (!isset($agg['buckets'])) {
            return [];
        }

        return $agg['buckets'];
    }

    public function countPmr(array $response): int
    {
        return $this->countTrue($response, 'pmr');
    }

    public function countMidi(array $response): int
    {
        return $this->countTrue($response, 'midi');
    }

    public function countCentreVille(array $response): int
    {
        return $this->countTrue($response, 'centre_ville');
    }

    private function countTrue(array $response, string $key): int
    {
        $agg = $this->getAggreations($response, $key);
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

    private function getAggreations(array $response, string $key): array
    {
        if (isset($response['aggregations'][$key])) {
            return $response['aggregations'][$key];
        }

        return [];
    }
}
