<?php

namespace AcMarche\Bottin\Cbe\Repository;

use AcMarche\Bottin\Cbe\Entity\Entreprise;
use Symfony\Component\Serializer\SerializerInterface;

class CbeRepository
{
    private ApiCbeRepository $apiCbeRepository;
    private SerializerInterface $serializer;

    public function __construct(ApiCbeRepository $apiCbeRepository, SerializerInterface $serializer)
    {
        $this->apiCbeRepository = $apiCbeRepository;
        $this->serializer = $serializer;
    }

    public function findByNumber(string $number): ?Entreprise
    {
        try {
            $cbeJson = $this->apiCbeRepository->getByNumber($number);
        } catch (\Exception $exception) {
            dump($exception);
        }

        $entreprise = $this->serializer->deserialize(
            $cbeJson,
            Entreprise::class,
            'json'
        );

        return $entreprise;
    }

}
