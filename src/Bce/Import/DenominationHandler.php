<?php

namespace AcMarche\Bottin\Bce\Import;

use AcMarche\Bottin\Bce\Entity\Denomination;
use AcMarche\Bottin\Bce\Repository\DenominationRepository;
use AcMarche\Bottin\Bce\Utils\SymfonyStyleFactory;

class DenominationHandler implements ImportHandlerInterface
{
    use SymfonyStyleFactory;

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
            if (!$this->denominationRepository->checkExist($data->entityNumber, $data->typeOfDenomination)) {
                $denomination = $data;
                $this->denominationRepository->persist($denomination);
            }
            $this->writeLn($data->denomination);
        }
        $this->denominationRepository->flush();
    }
}
