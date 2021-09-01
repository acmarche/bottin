<?php

namespace AcMarche\Bottin\Hades;

use AcMarche\Bottin\Hades\Entity\Hotel;
use AcMarche\Bottin\Hades\Entity\Hotel2;
use Exception;
use SimpleXMLElement;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class HadesRepository
{
    private HttpClientInterface $httpClient;
    private string $baseUrl;
    private SerializerInterface $serializer;

    /**
     * Hades constructor.
     */
    public function __construct(string $url, string $user, string $password, SerializerInterface $serializer)
    {
        $this->httpClient = HttpClient::create(
            [
                'auth_basic' => [$user, $password],
            ]
        );
        $this->baseUrl = $url;
        $this->serializer = $serializer;
    }

    public function getOffres(string $categorie): string
    {
        try {
            $request = $this->httpClient->request(
                'GET',
                $this->baseUrl,
                [
                    'query' => [
                        'tbl' => 'xmlcomplet',
                        //  'reg_id' => Hades::PAYS,
                        'com_id' => Hades::COMMUNE,
                        'cat_id' => $categorie,
                    ],
                ]
            );

            return $request->getContent();
        } catch (ClientException $e) {
            throw  new Exception($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function loadXml(string $xml): SimpleXMLElement
    {
        $data = simplexml_load_string($xml);

        if (false === $xml) {
            return libxml_get_errors();
        }

        return $data;
    }

    /**
     * @return Hotel2[]
     */
    public function getHotels(): array
    {
        $data = $this->loadXml($this->getOffres('hotel'));

        $hotels = [];

        foreach ($data as $item) {
            // var_dump($item->offre->asXML());
            $hotel = $this->serializer->deserialize($item->offre->asXML(), Hotel2::class, 'xml');
            var_dump($hotel);
            $hotels[] = $hotel;
            break;
        }

        return $hotels;
    }

    protected function getChambres(): array
    {
        $data = $this->loadXml($this->getOffres('chbre_chb,chbre_hote'));
        $hotels = [];

        foreach ($data as $item) {
            $hotel = $this->serializer->deserialize($item->asXML(), Hotel::class, 'xml');
            $hotels[] = $hotel;
        }

        return $hotels;
    }

    protected function getCamping(): array
    {
        $data = $this->loadXml($this->getOffres('camp_non_rec,camping'));
        $hotels = [];

        foreach ($data as $item) {
            $hotel = $this->serializer->deserialize($item->asXML(), Hotel::class, 'xml');
            $hotels[] = $hotel;
        }

        return $hotels;
    }

    protected function getGites(): array
    {
        $data = $this->loadXml($this->getOffres('git_ferme,git_citad,git_big_cap,git_rural,mbl_trm,mbl_vac'));
        $hotels = [];

        foreach ($data as $item) {
            $hotel = $this->serializer->deserialize($item->asXML(), Hotel::class, 'xml');
            $hotels[] = $hotel;
        }

        return $hotels;
    }
}
