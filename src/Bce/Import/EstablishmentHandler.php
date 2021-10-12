<?php

namespace AcMarche\Bottin\Bce\Import;

use AcMarche\Bottin\Bce\Entity\Establishment;
use AcMarche\Bottin\Bce\Repository\EstablishmentRepository;
use AcMarche\Bottin\Bce\Utils\CsvReader;

class EstablishmentHandler implements ImportHandlerInterface
{
    private EstablishmentRepository $establishmentRepository;
    private CsvReader $csvReader;

    public function __construct(EstablishmentRepository $establishmentRepository, CsvReader $csvReader)
    {
        $this->establishmentRepository = $establishmentRepository;
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
     * @param iterable|Establishment $data
     */
    public function handle($data)
    {
        if ($establishment = $this->establishmentRepository->checkExist($data->establishmentNumber)) {
            $establishment->enterpriseNumber = $data->enterpriseNumber;
            $establishment->startDate = $data->startDate;

        } else {
            $establishment = $data;
            $this->establishmentRepository->persist($establishment);
        }
    }

    /**
     * @param Establishment $data
     */
    public function writeLn($data): string
    {
        return $data->establishmentNumber;
    }

    public function flush(): void
    {
        $this->establishmentRepository->flush();
    }

    public static function getDefaultIndexName(): string
    {
        return 'establishment';
    }
}
