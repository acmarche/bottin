<?php

namespace AcMarche\Bottin\Hades;

use SimpleXMLElement;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;
use Symfony\Component\Serializer\Serializer;

class Hades
{
    public const COMMUNE = 263;
    public const CATEGORY_HOTELS = 649;

    private HadesRepository $hadesRepository;

    public function __construct(HadesRepository $hadesRepository)
    {
        $this->hadesRepository = $hadesRepository;
    }

    public function getProperties(): void
    {
        $data = $this->hadesRepository->loadXml($this->hadesRepository->getOffres('hotel'));

        foreach ($data as $offre) {
            foreach ($offre as $att) {
                /**
                 * @var SimpleXMLElement $att
                 */
                // print_r($att->asXML());
                // print_r($att->children());
                foreach (array_keys($att->children()) as $t) {
                    //  print_r($t);
                    //    print_r("private $".$t.";");
                }
            }
            break;
        }
    }

    public function desirialize(): void
    {
        $xml = $this->hadesRepository->getOffres('hotel');
        $normalizers = [
            new ObjectNormalizer(null, null, null, new ReflectionExtractor()),
            new ArrayDenormalizer(),
            new PropertyNormalizer(),
        ];
        $encoders = [new XmlEncoder()];
        $serializer = new Serializer($normalizers, $encoders);

        //return a array
        $decoded = $serializer->decode($xml, 'xml');
        //print_r($decoded);

        //retourne sous forme d'objet
        $denormalized = $serializer->denormalize($decoded, Response::class, 'xml');
        var_dump($denormalized);
    }

    public function getIdOffre(SimpleXMLElement $element): int
    {
        if (null !== $element->attributes() && null !== $element->attributes()->id) {
            return (int) $element->attributes()->id;
        }

        return 0;
    }
}
