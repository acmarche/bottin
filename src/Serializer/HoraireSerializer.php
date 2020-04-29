<?php


namespace AcMarche\Bottin\Serializer;


use AcMarche\Bottin\Entity\Horaire;
use Symfony\Component\Serializer\SerializerInterface;

class HoraireSerializer
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }
    public function serializeHoraireForApi(Horaire $horaire): array
    {
        $data = [];
        $data['id'] = $horaire->getId();
        $data['day'] = $horaire->getDay();
        $data['media_path'] = $horaire->getMediaPath();
        $data['is_open_at_lunch'] = (int)$horaire->getIsOpenAtLunch();
        $data['is_rdv'] = (int)$horaire->getIsRdv();
        $data['morning_start'] = $horaire->getMorningStart() != null ? $horaire->getMorningStart()->format(
            'H:i:s'
        ) : null;
        $data['morning_end'] = $horaire->getMorningEnd() != null ? $horaire->getMorningEnd()->format('H:i:s') : null;
        $data['noon_start'] = $horaire->getNoonStart() != null ? $horaire->getNoonStart()->format('H:i:s') : null;
        $data['noon_end'] = $horaire->getNoonEnd() != null ? $horaire->getNoonEnd()->format('H:i:s') : null;
        $data['created'] = $horaire->getCreatedAt()->format('Y-m-d H:i:s');
        $data['updated'] = $horaire->getUpdatedAt()->format('Y-m-d H:i:s');
        $data['createdAt'] = $horaire->getCreatedAt()->format('Y-m-d H:i:s');
        $data['updatedAt'] = $horaire->getUpdatedAt()->format('Y-m-d H:i:s');
        $data['fiche_id'] = $horaire->getFiche()->getId();
        $data['is_closed'] = (int)$horaire->getIsClosed();//force 1,0

        return $data;
    }
}
