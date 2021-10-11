<?php

namespace AcMarche\Bottin\Bce\Import;

use AcMarche\Bottin\Bce\Entity\Branch;
use AcMarche\Bottin\Bce\Repository\BranchRepository;

class BranchHandler implements ImportHandlerInterface
{
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
     * @param array|Branch[] $branchs
     */
    public function handle(array $branchs)
    {
        foreach ($branchs as $data) {
            if (!$this->branchRepository->checkExist($data->branch, $data->language, $data->category)) {
                $branch = $data;
                $this->branchRepository->persist($branch);
            }
        }
        $this->branchRepository->flush();
    }
}
