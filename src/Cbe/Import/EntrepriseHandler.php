<?php

namespace AcMarche\Bottin\Cbe\Import;

use AcMarche\Bottin\Cbe\Entity\Enterprise;
use AcMarche\Bottin\Cbe\Repository\EnterpriseRepository;

class EntrepriseHandler implements ImportHandlerInterface
{
    private EnterpriseRepository $entrepriseRepository;

    public function __construct(EnterpriseRepository $entrepriseRepository)
    {
        $this->entrepriseRepository = $entrepriseRepository;
    }

    public static function getDefaultIndexName(): string
    {
        return 'entreprise';
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
