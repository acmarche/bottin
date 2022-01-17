<?php

namespace AcMarche\Bottin\Serializer;

use AcMarche\Bottin\Entity\Horaire;

class HoraireSerializer
{
    public function serializeHoraireForApi(Horaire $horaire): array
    {
        $data = [];
        $data['id'] = $horaire->getId();
        $data['day'] = $horaire->getDay();
        $data['media_path'] = $horaire->getMediaPath();
        $data['is_open_at_lunch'] = (int) $horaire->getIsOpenAtLunch();
        $data['is_rdv'] = (int) $horaire->getIsRdv();
        $data['morning_start'] = $horaire->getMorningStart()?->format(
            'H:i:s'
        );
        $data['morning_end'] = $horaire->getMorningEnd()?->format('H:i:s');
        $data['noon_start'] = $horaire->getNoonStart()?->format('H:i:s');
        $data['noon_end'] = $horaire->getNoonEnd()?->format('H:i:s');
        $data['fiche_id'] = $horaire->getFiche()->getId();
        $data['is_closed'] = (int) $horaire->getIsClosed(); //force 1,0

        return $data;
    }
}
