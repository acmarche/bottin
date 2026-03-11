<?php

declare(strict_types=1);

return [
    'email' => env('WEBMASTER_EMAIL'),
    'meili' => [
        'index_name' => env('MEILISEARCH_INDEX_NAME'),
        'key' => env('MEILISEARCH_KEY'),
    ],
];
