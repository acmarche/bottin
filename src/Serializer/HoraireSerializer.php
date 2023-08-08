<?php

namespace AcMarche\Bottin\Serializer;

use AcMarche\Bottin\Entity\Horaire;

class HoraireSerializer
{
    public function serializeHoraireForApi(Horaire $horaire): array
    {
        return ['id' => $horaire->getId(), 'day' => $horaire->getDay(), 'media_path' => $horaire->getMediaPath(), 'is_open_at_lunch' => (int) $horaire->getIsOpenAtLunch(), 'is_rdv' => (int) $horaire->getIsRdv(), 'morning_start' => $horaire->getMorningStart()?->format(
            'H:i:s'
        ), 'morning_end' => $horaire->getMorningEnd()?->format('H:i:s'), 'noon_start' => $horaire->getNoonStart()?->format('H:i:s'), 'noon_end' => $horaire->getNoonEnd()?->format('H:i:s'), 'fiche_id' => $horaire->getFiche()->getId(), 'is_closed' => (int) $horaire->getIsClosed()];
    }
}
