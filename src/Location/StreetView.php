<?php

namespace AcMarche\Bottin\Location;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class StreetView
{
    private string $baseUrl;
    private HttpClientInterface $httpClient;
    private string $size;
    /**
     * @var ?int
     */
    private $heading;
    private int $fov;
    private int $pitch;
    private string $key;

    public function __construct(string $apiKeyGoogle)
    {
        $this->baseUrl = "https://maps.googleapis.com/maps/api/streetview";
        $this->httpClient = HttpClient::create();
        $this->size = "1024x768";
        $this->heading = null; //0 => 360 90 =>EST, 180 => SUD
        $this->fov = 90; //zoom 1 => 120
        $this->pitch = 0; //90 vers le haut, -90 vers le bas
        $this->key = $apiKeyGoogle;
    }

    public function getPhoto($latitude, $longitude)
    {
        $query = [
            'key' => $this->key,
            'location' => "$latitude, $longitude",
            'size' => $this->size,
            'fov' => $this->fov,
            'pitch' => $this->pitch
        ];

        if ($this->heading) {
            $query['heading'] = $this->heading;
        }

        try {
            $request = $this->httpClient->request(
                'GET',
                $this->baseUrl,
                [
                    'query' => $query
                ]
            );
        } catch (TransportExceptionInterface $e) {
        }

        try {
            return $request->getContent();
        } catch (ClientExceptionInterface $e) {
            return $this->createError($e->getMessage());
        } catch (RedirectionExceptionInterface $e) {
            return $this->createError($e->getMessage());
        } catch (ServerExceptionInterface $e) {
            return $this->createError($e->getMessage());
        } catch (TransportExceptionInterface $e) {
            return $this->createError($e->getMessage());
        }
    }

    protected function createError(string $message): array
    {
        return ['error' => true, 'message' => $message];
    }
}
