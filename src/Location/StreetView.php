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
    private ?int $heading = null;
    private int $fov;
    private int $pitch;

    public function __construct(private string $key)
    {
        $this->baseUrl = 'https://maps.googleapis.com/maps/api/streetview';
        $this->httpClient = HttpClient::create();
        $this->size = '1024x768';
        $this->heading = null; //0 => 360 90 =>EST, 180 => SUD
        $this->fov = 90; //zoom 1 => 120
        $this->pitch = 0;
    }

    public function getPhoto($latitude, $longitude): array|string
    {
        $request = null;
        $query = [
            'key' => $this->key,
            'location' => "$latitude, $longitude",
            'size' => $this->size,
            'fov' => $this->fov,
            'pitch' => $this->pitch,
        ];

        if ($this->heading) {
            $query['heading'] = $this->heading;
        }

        try {
            $request = $this->httpClient->request(
                'GET',
                $this->baseUrl,
                [
                    'query' => $query,
                ]
            );
        } catch (TransportExceptionInterface $e) {
        }

        try {
            return $request->getContent();
        } catch (ClientExceptionInterface|RedirectionExceptionInterface|ServerExceptionInterface|TransportExceptionInterface $e) {
            return $this->createError($e->getMessage());
        }
    }

    protected function createError(string $message): array
    {
        return ['error' => true, 'message' => $message];
    }
}
