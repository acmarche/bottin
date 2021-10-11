<?php

namespace AcMarche\Bottin\Bce\Import;

use AcMarche\Bottin\Bce\Entity\Enterprise;
use AcMarche\Bottin\Bce\Repository\EnterpriseRepository;

class EnterpriseHandler implements ImportHandlerInterface
{
    private EnterpriseRepository $entrepriseRepository;

    public function __construct(EnterpriseRepository $entrepriseRepository)
    {
        $this->entrepriseRepository = $entrepriseRepository;
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
        foreach ($entreprises as $data) {
            if (!$this->entrepriseRepository->checkExist($data->entreprise, $data->language, $data->category)) {
                $entreprise = $data;
                $this->entrepriseRepository->persist($entreprise);
            }
        }
        $this->entrepriseRepository->flush();
    }
}
