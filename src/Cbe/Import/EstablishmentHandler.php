<?php

namespace AcMarche\Bottin\Cbe\Import;

use AcMarche\Bottin\Cbe\Entity\Establishment;
use AcMarche\Bottin\Cbe\Repository\EstablishmentRepository;

class EstablishmentHandler implements ImportHandlerInterface
{
    private EstablishmentRepository $establishmentRepository;

    public function __construct(EstablishmentRepository $establishmentRepository)
    {
        $this->establishmentRepository = $establishmentRepository;
    }

    public static function getDefaultIndexName(): string
    {
        return 'establishment';
    }

    /**
     * @param array|Establishment[] $establishments
     */
    public function handle(array $establishments)
    {
        foreach ($establishments as $data) {
            if (!$this->establishmentRepository->checkExist($data->establishment, $data->language, $data->category)) {
                $establishment = $data;
                $this->establishmentRepository->persist($establishment);
            }
        }
        $this->establishmentRepository->flush();
    }
}
