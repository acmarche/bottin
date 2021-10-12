<?php

namespace AcMarche\Bottin\Bce\Import;

use AcMarche\Bottin\Bce\Entity\Branch;
use AcMarche\Bottin\Bce\Repository\BranchRepository;
use AcMarche\Bottin\Bce\Utils\SymfonyStyleFactory;

class BranchHandler implements ImportHandlerInterface
{
    use SymfonyStyleFactory;

    private BranchRepository $branchRepository;

    public function __construct(BranchRepository $branchRepository)
    {
        $this->branchRepository = $branchRepository;
    }

    public static function getDefaultIndexName(): string
    {
        return 'branch';
    }

    /**
     * @param iterable|Branch[] $branchs
     */
    public function handle(iterable $branchs)
    {
        foreach ($branchs as $data) {
            if (!$this->branchRepository->checkExist($data->id)) {
                $branch = $data;
                $this->branchRepository->persist($branch);
            }
            $this->writeLn($data->id);
        }
        $this->branchRepository->flush();
    }
}
