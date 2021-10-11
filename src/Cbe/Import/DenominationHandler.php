<?php

namespace AcMarche\Bottin\Cbe\Import;

use AcMarche\Bottin\Cbe\Entity\Denomination;
use AcMarche\Bottin\Cbe\Repository\DenominationRepository;

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
