<?php

namespace AcMarche\Bottin\Serializer;

use AcMarche\Bottin\Entity\Horaire;

class HoraireSerializer
{
    public function __construct()
    {
    }

    public function serializeHoraireForApi(Horaire $horaire): array
    {
        $data = [];
        $data['id'] = $horaire->getId();
        $data['day'] = $horaire->getDay();
        $data['media_path'] = $horaire->getMediaPath();
        $data['is_open_at_lunch'] = (int) $horaire->getIsOpenAtLunch();
        $data['is_rdv'] = (int) $horaire->getIsRdv();
        $data['morning_start'] = null != $horaire->getMorningStart() ? $horaire->getMorningStart()->format(
            'H:i:s'
        ) : null;
        $data['morning_end'] = null != $horaire->getMorningEnd() ? $horaire->getMorningEnd()->format('H:i:s') : null;
        $data['noon_start'] = null != $horaire->getNoonStart() ? $horaire->getNoonStart()->format('H:i:s') : null;
        $data['noon_end'] = null != $horaire->getNoonEnd() ? $horaire->getNoonEnd()->format('H:i:s') : null;
        $data['fiche_id'] = $horaire->getFiche()->getId();
        $data['is_closed'] = (int) $horaire->getIsClosed(); //force 1,0

        return $data;
    }
}
