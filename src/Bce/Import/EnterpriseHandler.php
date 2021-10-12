<?php

namespace AcMarche\Bottin\Bce\Import;

use AcMarche\Bottin\Bce\Entity\Enterprise;
use AcMarche\Bottin\Bce\Repository\EnterpriseRepository;
use AcMarche\Bottin\Bce\Utils\CsvReader;
use SplFileObject;

class EnterpriseHandler implements ImportHandlerInterface
{
    private EnterpriseRepository $enterpriseRepository;

    private CsvReader $csvReader;

    public function __construct(EnterpriseRepository $enterpriseRepository, CsvReader $csvReader)
    {
        $this->enterpriseRepository = $enterpriseRepository;
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
        if (!$enterprise = $this->enterpriseRepository->checkExist($data[0])) {
            $enterprise = new Enterprise();
            $enterprise->enterpriseNumber = $data[0];
            $this->enterpriseRepository->persist($enterprise);
        }

        $this->updateEnterprise($enterprise, $data);
    }

    /**
     * @return iterable|Enterprise[]
     */
    public function updateEnterprise(Enterprise $enterprise, array $data): ?Enterprise
    {
        $enterprise->status = $data[1];
        $enterprise->juridicalSituation = (int)$data[2];
        $enterprise->typeOfEnterprise = (int)$data[3];
        $enterprise->juridicalForm = (int)$data[4];
        $enterprise->startDate = $data[5];

        return $enterprise;
    }

    /**
     * @param array $data
     * @return string
     */
    public function writeLn($data): string
    {
        return $data[0];
    }

    public function flush(): void
    {
        $this->enterpriseRepository->flush();
    }

    public static function getDefaultIndexName(): string
    {
        return 'enterprise';
    }

    /**
     * Bug memory.
     */
    public function getEnterprisesCasse(string $file): iterable
    {
        $fileObj = new SplFileObject($file);

        $fileObj->setFlags(
            SplFileObject::READ_CSV
            | SplFileObject::SKIP_EMPTY
            | SplFileObject::READ_AHEAD
            | SplFileObject::DROP_NEW_LINE
        );
        $fileObj->setCsvControl(',');

        foreach ($fileObj as $data) {
            yield $data;
        }

        return $fileObj;
    }
}
