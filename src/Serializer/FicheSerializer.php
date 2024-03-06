<?php

namespace AcMarche\Bottin\Serializer;

use AcMarche\Bottin\Cap\CapService;
use AcMarche\Bottin\Entity\Fiche;
use Symfony\Component\Serializer\SerializerInterface;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class FicheSerializer
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly UploaderHelper $uploaderHelper
    ) {
    }

    public function serializeFicheForElastic(Fiche $fiche): array
    {
        $data = json_decode($this->serializeBaseFiche($fiche), true, 512, \JSON_THROW_ON_ERROR);
        $data['url_cap'] = CapService::generateUrlCap($fiche);
        $data['image'] = $this->firstImage($fiche);
        $data['slugname'] = $fiche->getSlug(); // @deprecated
        $data['tags'] = [];
        foreach ($fiche->tags as $tag) {
            if (!$tag->private) {
                $data['tags'][] = $tag->name;
            }
        }

        return $data;
    }

    private function firstImage(Fiche $fiche): ?string
    {
        if ($fiche->image()) {
            return $this->uploaderHelper->asset($fiche->imageFile(), 'image');
        }

        return '';
    }

    private function serializeBaseFiche(Fiche $fiche): string
    {
        return $this->serializer->serialize($fiche, 'json', ['groups' => 'group1']);
    }

    /**
     * @throws \JsonException
     */
    public function serializeFiche(Fiche $fiche): array
    {
        $data = json_decode($this->serializeBaseFiche($fiche), true, 512, \JSON_THROW_ON_ERROR);
        $data['updated_at'] = $fiche->getUpdatedAt()->format('Y-m-d');
        $data['created_at'] = $fiche->getCreatedAt()->format('Y-m-d');
        $data['slugname'] = $fiche->getSlug(); // @deprecated
        $data['google_plus'] = ''; // @deprecated
        $data['newsletter'] = ''; // @deprecated
        $data['newsletter_date'] = ''; // @deprecated
        $data['photos'] = [];
        $data['logo'] = '';
        $data['tags'] = [];
        foreach ($fiche->tags as $tag) {
            $data['tags'][$tag->getSlug()] = $tag->name;
        }

        return $data;
    }

}
