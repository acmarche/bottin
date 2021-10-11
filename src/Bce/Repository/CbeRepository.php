<?php

namespace AcMarche\Bottin\Bce\Repository;

use AcMarche\Bottin\Bce\Cache\CbeCache;
use AcMarche\Bottin\Bce\Entity\Enterprise;
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
    public function findByNumber(string $number): ?Enterprise
    {
        try {
            $cbeJson = $this->apiCbeRepository->getByNumber($number);
            $this->cbeCache->write($cbeJson, $number);

            $entreprise = $this->serializer->deserialize(
                $cbeJson,
                Enterprise::class,
                'json'
            );
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }

        return $entreprise;
    }

}
