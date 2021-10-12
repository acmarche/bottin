<?php

namespace AcMarche\Bottin\Bce\Import;

use AcMarche\Bottin\Bce\Entity\Denomination;
use AcMarche\Bottin\Bce\Repository\DenominationRepository;
use AcMarche\Bottin\Bce\Utils\CsvReader;

class DenominationHandler implements ImportHandlerInterface
{
    private DenominationRepository $denominationRepository;
    private CsvReader $csvReader;

    public function __construct(DenominationRepository $denominationRepository, CsvReader $csvReader)
    {
        $this->denominationRepository = $denominationRepository;
        $this->csvReader = $csvReader;
    }

    /**
     * @throws \Exception
     */
    public function readFile(string $fileName): iterable
    {
        return $this->csvReader->readFileAndConvertToClass($fileName);
    }

    /**
     * @param Denomination $data
     */
    public function handle($data)
    {
        if ($denomination = $this->denominationRepository->checkExist($data->entityNumber, $data->typeOfDenomination)) {
            $denomination->denomination = $data->denomination;
            $denomination->language = $data->language;
        } else {
            $denomination = $data;
            $this->denominationRepository->persist($denomination);
        }
    }

    /**
     * @param Denomination $data
     */
    public function writeLn($data): string
    {
        return $data->entityNumber;
    }

    public function flush(): void
    {
        $this->denominationRepository->flush();

    }

    public static function getDefaultIndexName(): string
    {
        return 'denomination';
    }
}
