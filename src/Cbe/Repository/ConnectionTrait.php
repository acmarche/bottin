<?php


namespace AcMarche\Bottin\Cbe\Repository;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

trait ConnectionTrait
{
    private HttpClientInterface $httpClient;
    private string $code;
    private string $url;
    private string $clef;
    private string $user;
    private string $password;
    private ?string $token;


}
