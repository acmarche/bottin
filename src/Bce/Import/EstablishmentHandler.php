<?php

namespace AcMarche\Bottin\Bce\Import;

use AcMarche\Bottin\Bce\Entity\Establishment;
use AcMarche\Bottin\Bce\Repository\EstablishmentRepository;
use AcMarche\Bottin\Bce\Utils\CsvReader;

class EstablishmentHandler implements ImportHandlerInterface
{
    private EstablishmentRepository $establishmentRepository;
    private CsvReader $csvReader;

    public function __construct(
        EstablishmentRepository $establishmentRepository,
        CsvReader $csvReader
    ) {
        $this->establishmentRepository = $establishmentRepository;
        $this->csvReader = $csvReader;
    }

    /**
     * @throws \Exception
     */
    public function readFile(string $fileName): iterable
    {
        return $this->csvReader->readCSVGenerator($fileName);
    }

    /**
     * @param array $data
     */
    public function handle($data)
    {
        if ('EstablishmentNumber' === $data[0]) {
            return;
        }
        if (!$establishment = $this->establishmentRepository->checkExist($data[0])) {
            $establishment = new Establishment();
            $establishment->establishmentNumber = $data[0];
            $this->establishmentRepository->persist($establishment);
        }
        $this->updateEstablishment($establishment, $data);
    }

    /**
     * "EstablishmentNumber","StartDate","EnterpriseNumber".
     */
    private function updateEstablishment(Establishment $establishment, array $data)
    {
        $establishment->startDate = $data[1];
        $establishment->enterpriseNumber = $data[2];
    }

    /**
     * @param array $data
     */
    public function writeLn($data): string
    {
        return $data[0];
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
