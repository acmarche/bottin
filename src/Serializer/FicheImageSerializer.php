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
        return ['id' => $ficheImage->getId(), 'fiche_id' => $ficheImage->fiche->getId(), 'principale' => $ficheImage->principale, 'image_name' => $ficheImage->imageName, 'mime' => $ficheImage->mime, 'updated_at' => $ficheImage->updatedAt->format('Y-m-d H:i:s')];
    }
}
