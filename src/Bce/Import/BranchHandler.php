<?php

namespace AcMarche\Bottin\Bce\Import;

use AcMarche\Bottin\Bce\Entity\Branch;
use AcMarche\Bottin\Bce\Repository\BranchRepository;
use AcMarche\Bottin\Bce\Utils\CsvReader;

class BranchHandler implements ImportHandlerInterface
{
    private BranchRepository $branchRepository;
    private CsvReader $csvReader;

    public function __construct(BranchRepository $branchRepository, CsvReader $csvReader)
    {
        $this->branchRepository = $branchRepository;
        $this->csvReader = $csvReader;
    }

    public function readFile(string $fileName): iterable
    {
        return $this->csvReader->readFileAndConvertToClass($fileName);
    }

    /**
     * @param Branch $data
     */
    public function handle($data)
    {
        if (!$this->branchRepository->checkExist($data->id)) {
            $branch = $data;
            $this->branchRepository->persist($branch);
        }
    }

    /**
     * @param Branch $data
     * @return string
     */
    public function writeLn($data): string
    {
        return $data->id;
    }

    public function flush(): void
    {
        $this->branchRepository->flush();
    }

    public static function getDefaultIndexName(): string
    {
        return 'branch';
    }

}
