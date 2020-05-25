<?php

namespace AcMarche\Bottin\Hades;

use AcMarche\Bottin\Hades\Entity\Offre;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;
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
        $data = $this->hadesRepository->loadXml($this->hadesRepository->getOffres('hotel'));

        foreach ($data as $offre) {
            foreach ($offre as $key => $att) {
                /**
                 * @var \SimpleXMLElement $att
                 */
                // print_r($att->asXML());
                // print_r($att->children());
                foreach ($att->children() as $t => $p) {
                    //  print_r($t);
                    //    print_r("private $".$t.";");
                }
            }
            break;
        }
    }

    public function desirialize()
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
