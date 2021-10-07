<?php

namespace AcMarche\Bottin\Cbe\Cache;

use AcMarche\Bottin\Cbe\Entity\Entreprise;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Serializer\SerializerInterface;

class CbeCache
{
    private ParameterBagInterface $parameterBag;
    private SerializerInterface $serializer;

    public function __construct(
        ParameterBagInterface $parameterBag,
        SerializerInterface $serializer
    ) {
        $this->parameterBag = $parameterBag;
        $this->serializer = $serializer;
    }

    public function getCacheData(string $number): ?Entreprise
    {
        $varDirectory = $this->parameterBag->get('kernel.project_dir').'/var/cbe';
        $file = $varDirectory.'/'.$number.'.json';

        if (is_readable($file)) {
            $cbeJson = file_get_contents($file);

            return $this->serializer->deserialize(
                $cbeJson,
                Entreprise::class,
                'json'
            );
        }

        return null;
    }

    public function write(string $cbeJson, string $number)
    {
        $file = $this->getVarDirectory().'/'.$number.'.json';
        $filesystem = new Filesystem();
        $filesystem->dumpFile($file, $cbeJson);
    }

    private function getVarDirectory(): string
    {
        return $this->parameterBag->get('kernel.project_dir').'/var/cbe';
    }
}
