<?php

namespace AcMarche\Bottin\Hades;

use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class Hades
{
    const COMMUNE = 263;
    const PAYS = 9;
    const CATEGORY_HOTELS = 649;
    const CATEGORY_CHMABRE = 651;
    const CATEGORY_GITE = 650;
    const CATEGORY_CAMPING = 652;

    /**
     * @var HadesRepository
     */
    private $hadesRepository;

    public function __construct(HadesRepository $hadesRepository)
    {
        $this->hadesRepository = $hadesRepository;
    }

    public function getProperties()
    {
        $data = $this->loadXml($this->getOffres('hotels'));
        foreach ($data as $item) {
            foreach ($item as $key => $att) {
                print_r("private $".$key.";");
            }
        }
    }

    public function desirialize(string $xml)
    {
        $normalizers = [
            new ObjectNormalizer(null, null, null, new ReflectionExtractor()),
            new ArrayDenormalizer(),
        ];
        $encoders = [new XmlEncoder()];
        $serializer = new Serializer($normalizers, $encoders);

        $decoded = $serializer->decode($xml, 'xml');
//var_dump($decoded);
        $denormalized = $serializer->denormalize($decoded, Response::class, 'xml');
        var_dump($denormalized);
    }

    public function getIdOffre(\SimpleXMLElement $element)
    {
        if ($element->attributes()) {
            if (isset($element->attributes()->id)) {
                return (int)$element->attributes()->id;
            }
        }

        return 0;
    }

}
