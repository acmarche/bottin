<?php

namespace AcMarche\Bottin\Cap;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CapApi
{
    private ?HttpClientInterface $httpClient = null;
    public ?string $url_executed = null;
    public ?string $data_raw = null;

    public function __construct(#[Autowire(env: 'CAP_URL')] private string $capUrl)
    {
    }

    /**
     * @throws \Exception
     */
    public function find(int $id): string
    {
        $this->connect();

        $url = $this->capUrl.'/bottin/'.$id;

        return $this->executeRequest($url);
    }

    public function shop(int $commercantId): string
    {
        $this->connect();

        $url = $this->capUrl.'/shop/'.$commercantId;

        return $this->executeRequest($url);
    }

    public function images(int $commercantId): string
    {
        $this->connect();

        $url = $this->capUrl.'/images/'.$commercantId;

        return $this->executeRequest($url);
    }

    public function connect(): void
    {
        if (!$this->httpClient === null) {
            return;
        }

        $this->httpClient = HttpClient::create();
    }

    /**
     * @throws \Exception
     */
    private function executeRequest(string $url, array $options = [], string $method = 'GET'): string
    {
        $this->url_executed = $url;
        try {
            $response = $this->httpClient->request(
                $method,
                $url,
                $options
            );

            $this->data_raw = $response->getContent();

            return $this->data_raw;
        } catch (ClientException|ClientExceptionInterface|RedirectionExceptionInterface|ServerExceptionInterface|TransportExceptionInterface $exception) {
            throw  new \Exception($exception->getMessage(), $exception->getCode(), $exception);
        }
    }


}