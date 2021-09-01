<?php

namespace AcMarche\Bottin\Elasticsearch;

use DateTimeInterface;
use Elastica\Query;
use Elastica\Query\BoolQuery;
use Elastica\Query\MatchQuery;
use Elastica\Query\MultiMatch;
use Elastica\Query\Range;
use Elastica\ResultSet;
use Elastica\Search;
use Psr\Log\LoggerInterface;

/**
 * https://github.com/ruflin/Elastica/tree/master/tests
 * Class Searcher.
 */
class Searcher
{
    use ElasticClientTrait;

    private ?BoolQuery $boolQuery = null;

    public function __construct(string $indexName, ?LoggerInterface $logger = null)
    {
        $this->connect($indexName);
        if (null !== $logger) {
            $this->client->setLogger($logger);
        }
    }

    public function search(iterable $args, array $constraints, int $limit = 50): ResultSet
    {
        $boolQuery = $this->createQuery($args, $constraints);

        $query = new Query();
        $query->addSort(['date_courrier' => 'asc']);
        $query->setQuery($boolQuery);

        $search = new Search($this->client);
        $search->addIndex($this->index);
        $search->setQuery($query);

        $options = ['limit' => $limit];

        return $search->search($query, $options);
    }

    private function createQuery(iterable $args, array $constraints): BoolQuery
    {
        $expediteur = $args['expediteur'] ?? null;
        $numero = $args['numero'] ?? null;
        $destinataire = $args['destinataire'] ?? null;
        $service = $args['service'] ?? null;
        $keyword = $args['nom'] ?? null;
        $date_fin = $args['date_fin'] ?? null;
        $date_debut = $args['date_debut'] ?? null;

        $this->boolQuery = new BoolQuery();

        if ($numero) {
            $match = new MatchQuery('numero', $numero);
            $this->boolQuery->addMust($match);

            return $this->boolQuery;
        }

        if ($keyword) {
            $match = new MultiMatch();
            $match->setFields(
                [
                    'description^1.2',
                    'description.stemmed',
                    'expediteur',
                    'expediteur.stemmed',
                    'destinataires',
                    'services',
                ]
            );
            $match->setQuery($keyword);
            $match->setType(MultiMatch::TYPE_MOST_FIELDS);
            $this->boolQuery->addMust($match);
        }

        if ($expediteur) {
            $match = new MatchQuery('expediteur', $expediteur);
            $matchStemmed = new MatchQuery('expediteur.stemmed', $expediteur);
            $this->boolQuery->addMust($match);
            $this->boolQuery->addMust($matchStemmed);
        }

        if (\count($constraints) > 0) {
            $this->addConstraints($constraints, $destinataire, $service);
        } else {
            if ($destinataire) {
                $match = new MatchQuery('destinataires', $destinataire->getId());
                $this->boolQuery->addMust($match);
            }

            if ($service) {
                $match = new MatchQuery('services', $service->getId());
                $this->boolQuery->addMust($match);
            }
        }

        $this->addDates($date_debut, $date_fin);

        return $this->boolQuery;
    }

    private function addDates(?DateTimeInterface $date_debut, ?DateTimeInterface $date_fin): void
    {
        if (null !== $date_debut) {
            $date_fin = null !== $date_fin ? $date_fin->format('Y-m-d') : $date_debut->format('Y-m-d');
            $date_debut = $date_debut->format('Y-m-d');
            $range = new Range('date_courrier', ['gte' => $date_debut, 'lte' => $date_fin]);
            $this->boolQuery->addMust($range);
        }
    }

    /**
     * si pas tous les droits
     * destinataires : 136 OR services :20 OR services: 28 OR services: 70 OR services: 65.
     */
    private function addConstraints(
        array $constraints,
        ?Destinataire $destinataire = null,
        ?Service $service = null
    ): void {
        $constraintQuery = new BoolQuery();

        /*
         * si le user n'a pas sélectionné de server et destinataire
         */
        if (!$destinataire && !$service) {
            foreach ($constraints['destinataires'] as $destina) {
                $match = new MatchQuery('destinataires', $destina->getId());
                $constraintQuery->addShould($match);
            }
            foreach ($constraints['services'] as $servi) {
                $match = new MatchQuery('services', $servi->getId());
                $constraintQuery->addShould($match);
            }
            $this->boolQuery->addMust($constraintQuery);

            return;
        }

        if (null !== $destinataire) {
            if (\in_array($destinataire, $constraints['destinataires'])) {
                $match = new MatchQuery('destinataires', $destinataire->getId());
                $constraintQuery->addMust($match);
            } else {
                $match = new MatchQuery('destinataires', 99_999_999); //anti fraude
                $constraintQuery->addMust($match);
            }
        }

        if (null !== $service) {
            if (\in_array($service, $constraints['services'])) {
                $match = new MatchQuery('services', $service->getId());
                $constraintQuery->addShould($match);
            } else {
                $match = new MatchQuery('services', 99_999_999); //anti fraude
                $constraintQuery->addMust($match);
            }
        }

        $this->boolQuery->addMust($constraintQuery);
    }

    public function byGeolocalistion(): void
    {
        /**
         * search.
         */
        $latitude = 50.2268;
        $longitude = 5.3442;
        $query = [
            'bool' => [
                'must' => [
                    'multi_match' => [
                        'query' => 'administrateur',
                        'fuzziness' => 'AUTO',
                        'fields' => [
                            'fonction',
                            'fonction.stemmed',
                        ],
                    ],
                ],
                'filter' => [
                    'geo_distance' => [
                        'distance' => '5km',
                        'location' => [5.3442, 50.2268],
                    ],
                ],
            ],
        ];
        $params = [
            'index' => 'bottin',
            'body' => [
                'profile' => 'true',
                'query' => $query,
                'aggs' => [
                    'centreville' => ['terms' => ['field' => 'centreville']],
                    'localite' => ['terms' => ['field' => 'localite']],
                    'pmr' => ['terms' => ['field' => 'pmr']],
                    'midi' => ['terms' => ['field' => 'midi']],
                ],
                'suggest' => [
                    'text' => 'Morche-en-Famenn',
                    'simple_phrase' => [
                        'phrase' => [
                            'field' => 'localite',
                            'size' => 1,
                            'gram_size' => 3,
                            'direct_generator' => [
                                [
                                    'field' => 'localite',
                                    'suggest_mode' => 'always',
                                    //"pre_filter" => "reverse",
                                    //"post_filter" => "reverse"
                                ],
                            ],
                            'highlight' => [
                                'pre_tag' => '<em>',
                                'post_tag' => '</em>',
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
