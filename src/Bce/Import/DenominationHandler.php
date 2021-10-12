<?php

namespace AcMarche\Bottin\Bce\Import;

use AcMarche\Bottin\Bce\Entity\Denomination;
use AcMarche\Bottin\Bce\Repository\DenominationRepository;
use AcMarche\Bottin\Bce\Utils\SymfonyStyleFactory;

class DenominationHandler
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
     * @param iterable|Denomination[] $denominations
     */
    public function handle(iterable $denominations):?object
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
    public function flush(): void
    {
        // TODO: Implement flush() method.
    }
}
