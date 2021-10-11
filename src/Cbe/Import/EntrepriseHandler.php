<?php

namespace AcMarche\Bottin\Cbe\Import;

use AcMarche\Bottin\Cbe\Entity\Entreprise;
use AcMarche\Bottin\Cbe\Repository\EntrepriseRepository;

class EntrepriseHandler implements ImportHandlerInterface
{
    private EntrepriseRepository $entrepriseRepository;

    public function __construct(EntrepriseRepository $entrepriseRepository)
    {
        $this->entrepriseRepository = $entrepriseRepository;
    }

    public static function getDefaultIndexName(): string
    {
        return 'entreprise';
    }

    /**
     * @param array|Entreprise[] $entreprises
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
