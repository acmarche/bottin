<?php


namespace AcMarche\Bottin\Serializer;


use AcMarche\Bottin\Entity\FicheImage;
use Symfony\Component\Serializer\SerializerInterface;

class FicheImageSerializer
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    public function serializeFicheImage(FicheImage $ficheImage)
    {
        //$data = json_decode($this->serializer->serialize($ficheImage, 'json', ['group1']), true);
        $data = [];
        $data['id'] = $ficheImage->getId();
        $data['fiche_id'] = $ficheImage->getFiche()->getId();
        $data['principale'] = $ficheImage->getPrincipale();
        $data['image_name'] = $ficheImage->getImageName();
        $data['mime'] = $ficheImage->getMime();
        $data['updated_at'] = $ficheImage->getUpdatedAt()->format('Y-m-d H:i:s');

        return $data;
    }
}
