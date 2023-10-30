<?php

namespace AcMarche\Bottin\Serializer;

use AcMarche\Bottin\Entity\Horaire;

class HoraireSerializer
{
    public function serializeHoraireForApi(Horaire $horaire): array
    {
        return [
            'id' => $horaire->getId(),
            'day' => $horaire->day,
            'media_path' => $horaire->media_path,
            'is_open_at_lunch' => (int)$horaire->is_open_at_lunch,
            'is_rdv' => (int)$horaire->is_rdv,
            'morning_start' => $horaire->morning_start?->format(
                'H:i:s'
            ),
            'morning_end' => $horaire->morning_end?->format('H:i:s'),
            'noon_start' => $horaire->noon_start?->format('H:i:s'),
            'noon_end' => $horaire->noon_end?->format('H:i:s'),
            'fiche_id' => $horaire->fiche->getId(),
            'is_closed' => (int)$horaire->is_closed,
        ];
    }
}
