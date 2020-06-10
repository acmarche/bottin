<?php


namespace AcMarche\Bottin\Service;

use AcMarche\Bottin\Entity\Fiche;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GeolocalisationService
{
    /**
     * @var string
     */
    private $clefGoogle;

    /**
     * @var string
     */
    private $urlGoogle = 'https://maps.googleapis.com/maps/api/geocode/json';

    /**
     * @var HttpClientInterface
     */
    private $httpClient;

    public function __construct(HttpClientInterface $httpClient, ParameterBagInterface $parameterBag)
    {
        $this->clefGoogle = $parameterBag->get('bottin.api_key_geocode');
        $this->httpClient = $httpClient;
    }

    /**
     * @param Fiche $fiche
     *
     * @return array
     *
     * @throws \Exception
     */
    public function convertToCoordonate(Fiche $fiche, $withNum = true)
    {
        $location = [];

        $adresse = urlencode($fiche->getAdresse($withNum) . ' BE');

        try {
            $request = $this->httpClient->request(
                'GET',
                $this->urlGoogle,
                [
                    'query' => [
                        'address' => $adresse,
                        'key' => $this->clefGoogle,
                    ]
                ]
            );
        } catch (TransportExceptionInterface $exception) {
            throw new \Exception($exception->getMessage());
        }

        try {
            $content = $request->getContent();
        } catch (ClientExceptionInterface $exception) {
            throw new \Exception($exception->getMessage());
        } catch (RedirectionExceptionInterface $exception) {
            throw new \Exception($exception->getMessage());
        } catch (ServerExceptionInterface $exception) {
            throw new \Exception($exception->getMessage());
        } catch (TransportExceptionInterface $exception) {
            throw new \Exception($exception->getMessage());
        }

        $coordonates = json_decode($content, true);

        if (is_array($coordonates)) {
            if (isset($coordonates['error_message'])) {
                throw new \Exception($coordonates['error_message']);
            }

            $status = $coordonates['status'];
            if ('ZERO_RESULTS' === $status) {
                throw new \Exception('Pas d\'adresse postale');
            }

            $results = $coordonates['results'];

            $geometry = $results[0]['geometry'];

            $location = $geometry['location'];
            $fiche->setLatitude($location['lat']);
            $fiche->setLongitude($location['lng']);
        }

        return $location;
    }
}
