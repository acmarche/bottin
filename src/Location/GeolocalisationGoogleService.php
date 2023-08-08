<?php

namespace AcMarche\Bottin\Location;

use AcMarche\Bottin\Entity\Fiche;
use Exception;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GeolocalisationGoogleService
{
    private readonly string $clefGoogle;

    private string $urlGoogle = 'https://maps.googleapis.com/maps/api/geocode/json';

    public function __construct(private readonly HttpClientInterface $httpClient, ParameterBagInterface $parameterBag)
    {
        $this->clefGoogle = $parameterBag->get('bottin.api_key_geocode');
    }

    /**
     * @throws Exception
     */
    public function convertToCoordonate(Fiche $fiche, $withNum = true): array
    {
        $location = [];

        $adresse = urlencode($this->getAdresseGeocode($fiche, $withNum).' BE');

        try {
            $request = $this->httpClient->request(
                'GET',
                $this->urlGoogle,
                [
                    'query' => [
                        'address' => $adresse,
                        'key' => $this->clefGoogle,
                    ],
                ]
            );
        } catch (TransportExceptionInterface $transportException) {
            throw new Exception($transportException->getMessage(), $transportException->getCode(), $transportException);
        }

        try {
            $content = $request->getContent();
        } catch (ClientExceptionInterface|RedirectionExceptionInterface|ServerExceptionInterface|TransportExceptionInterface $exception) {
            throw new Exception($exception->getMessage(), $exception->getCode(), $exception);
        }

        $coordonates = json_decode($content, true, 512, JSON_THROW_ON_ERROR);

        if (\is_array($coordonates)) {
            if (isset($coordonates['error_message'])) {
                throw new Exception($coordonates['error_message']);
            }

            $status = $coordonates['status'];
            if ('ZERO_RESULTS' === $status) {
                throw new Exception("Pas d'adresse postale");
            }

            $results = $coordonates['results'];

            $geometry = $results[0]['geometry'];

            $location = $geometry['location'];
            $fiche->setLatitude($location['lat']);
            $fiche->setLongitude($location['lng']);
        }

        return $location;
    }

    private function getAdresseGeocode(Fiche $fiche, bool $withNumero = true): ?string
    {
        if ($fiche->getRue()) {
            $adresse = '';
            if ($fiche->getNumero() && $withNumero) {
                $adresse = $fiche->getNumero().' ';
            }

            return $adresse.$fiche->getRue().' '.$fiche->getCp().' '.$fiche->getLocalite().' Belgium';
        } else {
            return 'Rue du Commerce Marche-en-Famenne Beligum';
        }
    }
}
