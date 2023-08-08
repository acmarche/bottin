<?php

namespace AcMarche\Bottin\Serializer;

use AcMarche\Bottin\Entity\FicheImage;

class FicheImageSerializer
{
    public function __construct()
    {
    }

    public function serializeFicheImage(FicheImage $ficheImage): array
    {
        return ['id' => $ficheImage->getId(), 'fiche_id' => $ficheImage->getFiche()->getId(), 'principale' => $ficheImage->getPrincipale(), 'image_name' => $ficheImage->getImageName(), 'mime' => $ficheImage->getMime(), 'updated_at' => $ficheImage->getUpdatedAt()->format('Y-m-d H:i:s')];
    }
}
