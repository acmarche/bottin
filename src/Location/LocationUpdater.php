<?php

namespace AcMarche\Bottin\Location;

use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class LocationUpdater
{
    public function __construct(private readonly LocationInterface $location)
    {
    }

    public function convertAddressToCoordinates(LocationAbleInterface $locationAble): bool
    {
        if (!$locationAble->getRue()) {
            throw new \Exception('Aucune rue encodée, pas de données de géolocalisation');
        }

        try {
            $response = $this->location->search($this->getAdresseString($locationAble));
            $tab = json_decode((string) $response, true, 512, \JSON_THROW_ON_ERROR);

            if (\is_array($tab) && 0 == \count($tab)) {
                throw new \Exception('L\'adresse n\'a pas pu être convertie en latitude longitude:'.$response);
            }

            if (false == $tab) {
                throw new \Exception('Decode json error:'.$response);
            }

            if (\is_array($tab) && [] !== $tab) {
                $this->setCoordinates($locationAble, $tab);

                return true;
            } else {
                throw new \Exception('Convertion en latitude longitude error:'.$response);
            }
        } catch (\JsonException|ClientExceptionInterface|RedirectionExceptionInterface|TransportExceptionInterface|ServerExceptionInterface $e) {
            throw new \Exception($e->getMessage(), $e->getCode(), $e);
        }
    }

    private function setCoordinates(LocationAbleInterface $locationAble, array $data): void
    {
        $locationAble->setLatitude($data[0]['lat']);
        $locationAble->setLongitude($data[0]['lon']);
    }

    private function getAdresseString(LocationAbleInterface $locationAble): string
    {
        return $locationAble->getNumero().' '.
            $locationAble->getRue().', '.
            $locationAble->getCp().' '.
            $locationAble->getLocalite();
    }
}
