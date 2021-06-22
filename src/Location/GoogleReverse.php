<?php


namespace AcMarche\Bottin\Location;

use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * https://developers.google.com/maps/documentation/geocoding/start
 * Class GoogleReverse
 * @package AcMarche\Avaloir\Location
 */
class GoogleReverse implements LocationReverseInterface
{
    private string $apiKeyGoogle;
    private string $baseUrl;
    private HttpClientInterface $httpClient;
    private array $result = [];

    public function __construct(string $apiKeyGoogle)
    {
        $this->baseUrl = 'https://maps.googleapis.com/maps/api/geocode/json';
        $this->httpClient = HttpClient::create();
        $this->apiKeyGoogle = $apiKeyGoogle;
    }

    /**
     * @param $latitude
     * @param $longitude
     * @return array
     */
    public function reverse($latitude, $longitude): array
    {
        try {
            $request = $this->httpClient->request(
                'GET',
                $this->baseUrl,
                [
                    'query' => [
                        //'location_type' => 'ROOFTOP',
                        'result_type' => 'street_address',
                        'key' => $this->apiKeyGoogle,
                        'latlng' => $latitude . ',' . $longitude,
                    ],
                ]
            );

            $this->result = json_decode($request->getContent(), true);

            return $this->result;
        } catch (ClientException $e) {
            return ['error' => true, 'message' => $e->getMessage()];
        } catch (TransportExceptionInterface $e) {
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
