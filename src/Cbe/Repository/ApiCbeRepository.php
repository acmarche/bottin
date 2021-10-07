<?php

namespace AcMarche\Bottin\Cbe\Repository;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class ApiCbeRepository
{
    private HttpClientInterface $httpClient;
    private string $clientId;
    private string $secretKey;

    public function __construct()
    {
        $this->url = $_ENV['CBE_URL'];
        $this->clientId = $_ENV['CBE_ID'];
        $this->secretKey = $_ENV['CBE_KEY'];
    }

    private function connect()
    {
        $this->httpClient = HttpClient::create();
    }

    /**
     * @throws \Exception
     * @throws TransportExceptionInterface
     */
    public function getByNumber(string $number): string
    {
        $this->connect();

        $request = $this->httpClient->request(
                'POST',
                $this->url.'/byCBE',
                [
                    'json' => [
                        'clientId' => $this->clientId,
                        'secretKey' => $this->secretKey,
                        'data' => [
                            'cbe' => $number,
                        ],
                    ],
                ]
            );

        return $this->getContent($request, $number);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws \Exception
     */
    public function getContent(ResponseInterface $request, string $number): string
    {
        $statusCode = $request->getStatusCode();
        if (404 === $statusCode) {
            throw new \Exception("Aucune entreprise trouvée avec le numéro '.$number.'");
        }

        if (400 === $statusCode) {
            throw new \Exception('Your quota limit is reached');
        }

        try {
            return $request->getContent();
        } catch (ClientExceptionInterface | TransportExceptionInterface | ServerExceptionInterface | RedirectionExceptionInterface $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
