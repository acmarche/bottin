<?php


namespace AcMarche\Bottin\Hades;

use AcMarche\Bottin\Hades\Entity\Hotel;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class HadesRepository
{
    /**
     * @var HttpClientInterface
     */
    private $httpClient;
    /**
     * @var string
     */
    private $baseUrl;
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * Hades constructor.
     */
    public function __construct(string $url, SerializerInterface $serializer)
    {
        $this->httpClient = HttpClient::create();
        $this->baseUrl = $url;
        $this->serializer = $serializer;
    }

    public function getOffres(string $categorie)
    {
        try {
            $request = $this->httpClient->request(
                'GET',
                $this->baseUrl,
                [
                    'query' => [
                        'tbl' => 'xmlcomplet',
                        //  'pays' => Hades::PAYS,
                        'com_id' => Hades::COMMUNE,
                        'quoi' => 'tout',
                        'offre' => $categorie,
                    ],
                ]
            );

            return $request->getContent();
        } catch (ClientException $e) {
            throw  new \Exception($e->getMessage());
        }
    }

    public function loadXml(string $xml): \SimpleXMLElement
    {
        $data = simplexml_load_string($xml);

        if (false === $xml) {
            return libxml_get_errors();
        }

        return $data;
    }

    /**
     * @return Hotel[]
     */
    public function getHotels()
    {
        $data = $this->loadXml($this->getOffres('hotels'));
        print_r($data);

        $hotels = [];

        foreach ($data as $item) {
            $hotel = $this->serializer->deserialize($item->asXML(), Hotel::class, 'xml');
            $hotels[] = $hotel;
        }

        return $hotels;
    }

    protected function getChambres()
    {
        $data = $this->loadXml($this->getOffres('chbre_chb,chbre_hote'));
        $hotels = [];

        foreach ($data as $item) {
            $hotel = $this->serializer->deserialize($item->asXML(), Hotel::class, 'xml');
            $hotels[] = $hotel;
        }

        return $hotels;
    }

    protected function getCamping()
    {
        $data = $this->loadXml($this->getOffres('camp_non_rec,camping'));
        $hotels = [];

        foreach ($data as $item) {
            $hotel = $this->serializer->deserialize($item->asXML(), Hotel::class, 'xml');
            $hotels[] = $hotel;
        }

        return $hotels;
    }

    protected function getGites()
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
