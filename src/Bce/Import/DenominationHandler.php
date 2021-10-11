<?php

namespace AcMarche\Bottin\Bce\Import;

use AcMarche\Bottin\Bce\Entity\Denomination;
use AcMarche\Bottin\Bce\Repository\DenominationRepository;

class DenominationHandler implements ImportHandlerInterface
{
    private DenominationRepository $denominationRepository;

    public function __construct(DenominationRepository $denominationRepository)
    {
        $this->denominationRepository = $denominationRepository;
    }

    public static function getDefaultIndexName(): string
    {
        return 'denomination';
    }

    /**
     * @param array|Denomination[] $denominations
     */
    public function handle(array $denominations)
    {
        foreach ($denominations as $data) {
            if (!$this->denominationRepository->checkExist($data->denomination, $data->language, $data->category)) {
                $denomination = $data;
                $this->denominationRepository->persist($denomination);
            }
        }
        $this->denominationRepository->flush();
    }
}
