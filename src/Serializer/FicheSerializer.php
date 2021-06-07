<?php


namespace AcMarche\Bottin\Serializer;


use AcMarche\Bottin\Cap\CapService;
use AcMarche\Bottin\Entity\Fiche;
use Symfony\Component\Serializer\SerializerInterface;

class FicheSerializer
{
    private \Symfony\Component\Serializer\SerializerInterface $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    public function serializeFicheForElastic(Fiche $fiche): array
    {
        $data = json_decode($this->serializeBaseFiche($fiche), true);
        $data['url_cap'] = CapService::generateUrlCap($fiche);
        $data['slugname'] = $fiche->getSlug();//@deprecated

        return $data;
    }

    private function serializeBaseFiche(Fiche $fiche): string
    {
        return $this->serializer->serialize($fiche, 'json', ['groups' => 'group1']);
    }

    public function serializeFiche(Fiche $fiche): array
    {
        $data = json_decode($this->serializeBaseFiche($fiche), true);
        $data['updated_at'] = $fiche->getUpdatedAt()->format('Y-m-d');
        $data['created_at'] = $fiche->getCreatedAt()->format('Y-m-d');
        $data['slugname'] = $fiche->getSlug();//@deprecated
        $data['google_plus'] = '';//@deprecated
        $data['newsletter'] = '';//@deprecated
        $data['newsletter_date'] = '';//@deprecated
        $data['photos'] = [];
        $data['logo'] = '';

        return $data;
    }

    public function serialize(Fiche $fiche)
    {
        $data = json_decode($this->serializeBaseFiche($fiche), true);
        if ($fiche->getLatitude() && $fiche->getLongitude()) {
            $std['location'] = ["lat" => $fiche->getLatitude(), "lon" => $fiche->getLongitude()];
        }

        return $data;
    }

}
