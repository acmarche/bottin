<?php

namespace AcMarche\Bottin\Location;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class LocationOpenStreetMap implements LocationInterface
{
    private readonly string $baseUrl;

    private readonly HttpClientInterface $httpClient;

    public function __construct()
    {
        $this->baseUrl = 'https://nominatim.openstreetmap.org/search';
        $this->httpClient = HttpClient::create();
    }

    /**
     * @return mixed
     *
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function search(string $query): string
    {
        $response = $this->httpClient->request(
            'GET',
            $this->baseUrl,
            [
                'query' => [
                    'format' => 'json',
                    'q' => $query.' Belgium',
                    'addressdetails' => 1,
                ],
            ]
        );

        return $response->getContent();
    }
}
