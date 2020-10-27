<?php


namespace AcMarche\Bottin\Location;


use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class LocationOpenStreetMap implements LocationInterface
{
    /**
     * @var string
     */
    private $baseUrl;
    /**
     * @var HttpClientInterface
     */
    private $client;

    public function __construct()
    {
        $this->baseUrl = 'https://nominatim.openstreetmap.org/search';
        $this->client = HttpClient::create();
    }

    /**
     * @param string $query
     * @return mixed
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function search(string $query)
    {
        $response = $this->client->request(
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
