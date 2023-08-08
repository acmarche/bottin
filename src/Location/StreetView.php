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
    private readonly string $baseUrl;

    private readonly HttpClientInterface $httpClient;

    private readonly string $size;

    private ?int $heading = null;

    private readonly int $fov;

    private readonly int $pitch;

    public function __construct(private readonly string $key)
    {
        $this->baseUrl = 'https://maps.googleapis.com/maps/api/streetview';
        $this->httpClient = HttpClient::create();
        $this->size = '1024x768'; // 0 => 360 90 =>EST, 180 => SUD
        $this->fov = 90; // zoom 1 => 120
        $this->pitch = 0;
    }

    public function getPhoto($latitude, $longitude): array|string
    {
        $request = null;
        $query = [
            'key' => $this->key,
            'location' => sprintf('%s, %s', $latitude, $longitude),
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
        } catch (TransportExceptionInterface $transportException) {
        }

        try {
            return $request->getContent();
        } catch (ClientExceptionInterface|RedirectionExceptionInterface|ServerExceptionInterface|TransportExceptionInterface $transportException) {
            return $this->createError($transportException->getMessage());
        }
    }

    protected function createError(string $message): array
    {
        return ['error' => true, 'message' => $message];
    }
}
