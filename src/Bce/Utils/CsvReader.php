<?php

namespace AcMarche\Bottin\Bce\Utils;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class CsvReader
{
    private SerializerInterface $serializer;
    private ParameterBagInterface $parameterBag;

    public function __construct(
        ParameterBagInterface $parameterBag,
        SerializerInterface $serializer
    ) {
        $this->serializer = $serializer;
        $this->parameterBag = $parameterBag;
    }

    /**
     * @throws \Exception
     */
    public function readFile(string $fileName): array
    {
        $varDirectory = $this->parameterBag->get('kernel.project_dir').'/var/cbe';
        $file = $varDirectory.'/'.$fileName.'.csv';

        if (!is_readable($file)) {
            throw new \Exception('File not found '.$file);
        }

        $objects = [];
        $class = 'AcMarche\Bottin\Bce\Entity\\'.ucfirst($fileName).'[]';
        try {
            $objects = $this->serializer->deserialize(file_get_contents($file), $class, 'csv', [

            ]);
        } catch (\Exception$exception) {
            dump($exception->getMessage());
        }

        return $objects;
    }
}
