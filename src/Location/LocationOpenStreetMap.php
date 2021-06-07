<?php


namespace AcMarche\Bottin\Location;


use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class LocationOpenStreetMap implements LocationInterface
{
    private string $baseUrl;
    private \Symfony\Contracts\HttpClient\HttpClientInterface $httpClient;

    public function __construct()
    {
        $this->baseUrl = 'https://nominatim.openstreetmap.org/search';
        $this->httpClient = HttpClient::create();
    }

    /**
     * @param string $query
     * @return mixed
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
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
