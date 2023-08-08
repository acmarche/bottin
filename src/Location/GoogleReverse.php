<?php

namespace AcMarche\Bottin\Location;

use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * https://developers.google.com/maps/documentation/geocoding/start
 * Class GoogleReverse.
 */
class GoogleReverse implements LocationReverseInterface
{
    private readonly string $baseUrl;

    private readonly HttpClientInterface $httpClient;

    private array $result = [];

    public function __construct(private readonly string $apiKeyGoogle)
    {
        $this->baseUrl = 'https://maps.googleapis.com/maps/api/geocode/json';
        $this->httpClient = HttpClient::create();
    }

    public function reverse($latitude, $longitude): array
    {
        try {
            $request = $this->httpClient->request(
                'GET',
                $this->baseUrl,
                [
                    'query' => [
                        // 'location_type' => 'ROOFTOP',
                        'result_type' => 'street_address',
                        'key' => $this->apiKeyGoogle,
                        'latlng' => $latitude.','.$longitude,
                    ],
                ]
            );

            $this->result = json_decode($request->getContent(), true, 512, \JSON_THROW_ON_ERROR);

            return $this->result;
        } catch (ClientException|TransportExceptionInterface $e) {
            return ['error' => true, 'message' => $e->getMessage()];
        }
    }

    public function getRoad(): ?string
    {
        $results = $this->result['results'];
        $first = $results[0];

        return $first['address_components'][1]['long_name'];
    }

    public function getLocality(): ?string
    {
        $results = $this->result['results'];
        $first = $results[0];

        return $first['address_components'][2]['long_name'];
    }

    public function getHouseNumber(): ?string
    {
        return null;
    }
}
