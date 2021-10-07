<?php

namespace AcMarche\Bottin\Cbe\Repository;

use AcMarche\Bottin\Cbe\Cache\CbeCache;
use AcMarche\Bottin\Cbe\Entity\Entreprise;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class CbeRepository
{
    private ApiCbeRepository $apiCbeRepository;
    private SerializerInterface $serializer;
    private CbeCache $cbeCache;

    public function __construct(ApiCbeRepository $apiCbeRepository, SerializerInterface $serializer, CbeCache $cbeCache)
    {
        $this->apiCbeRepository = $apiCbeRepository;
        $this->serializer = $serializer;
        $this->cbeCache = $cbeCache;
    }

    /**
     * @throws TransportExceptionInterface
     * @throws \Exception
     */
    public function findByNumber(string $number): ?Entreprise
    {
        try {
            $cbeJson = $this->apiCbeRepository->getByNumber($number);
            $this->cbeCache->write($cbeJson, $number);

            $entreprise = $this->serializer->deserialize(
                $cbeJson,
                Entreprise::class,
                'json'
            );
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }

        return $entreprise;
    }

}
