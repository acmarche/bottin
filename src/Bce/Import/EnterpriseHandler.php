<?php

namespace AcMarche\Bottin\Bce\Import;

use AcMarche\Bottin\Bce\Entity\Enterprise;
use AcMarche\Bottin\Bce\Repository\EnterpriseRepository;
use AcMarche\Bottin\Bce\Utils\SymfonyStyleFactory;

class EnterpriseHandler implements ImportHandlerInterface
{
    use SymfonyStyleFactory;

    private EnterpriseRepository $enterpriseRepository;

    public function __construct(EnterpriseRepository $enterpriseRepository)
    {
        $this->enterpriseRepository = $enterpriseRepository;
    }

    public static function getDefaultIndexName(): string
    {
        return 'enterprise';
    }

    /**
     * @param array|Enterprise[] $entreprises
     */
    public function handle(array $entreprises)
    {
        dump(123);
        foreach ($entreprises as $data) {
            if (!$this->enterpriseRepository->checkExist($data->enterpriseNumber)) {
                $enterprise = $data;
                $this->enterpriseRepository->persist($enterprise);
            }
            $this->writeLn($data->enterpriseNumber);
        }
        $this->enterpriseRepository->flush();
    }
}
