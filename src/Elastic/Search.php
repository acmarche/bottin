<?php

namespace AcMarche\Bottin\Elastic;

class Search
{
    function byGeolocalistion(): void
    {
        /**
         * search
         */
        $latitude = 50.2268;
        $longitude = 5.3442;
        $query = [
            "bool" => [
                "must" => [
                    "multi_match" => [
                        "query" => "administrateur",
                        "fuzziness" => "AUTO",
                        "fields" => [
                            "fonction",
                            "fonction.stemmed"
                        ]
                    ]
                ],
                "filter" => [
                    "geo_distance" => [
                        "distance" => "5km",
                        "location" => [5.3442, 50.2268]
                    ]
                ]
            ],
        ];
        $params = [
            'index' => 'bottin',
            'body' => [
                'profile' => 'true',
                'query' => $query,
                "aggs" => [
                    "centreville" => ["terms" => ["field" => "centreville"]],
                    "localite" => ["terms" => ["field" => "localite"]],
                    "pmr" => ["terms" => ["field" => "pmr"]],
                    "midi" => ["terms" => ["field" => "midi"]]
                ],
                "suggest" => [
                    "text" => "Morche-en-Famenn",
                    "simple_phrase" => [
                        "phrase" => [
                            "field" => "localite",
                            "size" => 1,
                            "gram_size" => 3,
                            "direct_generator" => [
                                [
                                    "field" => "localite",
                                    "suggest_mode" => "always",
                                    //"pre_filter" => "reverse",
                                    //"post_filter" => "reverse"
                                ]
                            ],
                            "highlight" => [
                                "pre_tag" => "<em>",
                                "post_tag" => "</em>"
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }
}
