<?php


namespace AcMarche\Bottin\Service;

use AcMarche\Bottin\Entity\Demande;
use AcMarche\Bottin\Entity\DemandeMeta;
use AcMarche\Bottin\Repository\DemandeMetaRepository;
use AcMarche\Bottin\Repository\DemandeRepository;
use AcMarche\Bottin\Repository\FicheRepository;

class DemandeHandler
{
    /**
     * @var FicheRepository
     */
    private $ficheRepository;
    /**
     * @var DemandeRepository
     */
    private $demandeRepository;
    /**
     * @var DemandeMetaRepository
     */
    private $demandeMetaRepository;
    /**
     * @var MailerBottin
     */
    private $mailerBottin;

    public function __construct(
        FicheRepository $ficheRepository,
        DemandeRepository $demandeRepository,
        DemandeMetaRepository $demandeMetaRepository,
        MailerBottin $mailerBottin
    ) {
        $this->ficheRepository = $ficheRepository;
        $this->demandeRepository = $demandeRepository;
        $this->demandeMetaRepository = $demandeMetaRepository;
        $this->mailerBottin = $mailerBottin;
    }

    public function handle(array $data): array
    {
        $idFiche = $data['id'];
        if (!$idFiche) {
            return ['error' => 402];
        }

        $fiche = $this->ficheRepository->find($idFiche);
        if (!$fiche) {
            return ['error' => 404];
        }

        $demande = new Demande();
        $demande->setFiche($fiche);
        $this->demandeRepository->persist($demande);
        foreach ($data as $key => $value) {
            if ($key == 'id') {
                continue;
            }
            $demandeMeta = new DemandeMeta($demande, $key, $value);
            $demande->addMeta($demandeMeta);
            $this->demandeMetaRepository->persist($demandeMeta);
        }

        $this->demandeRepository->flush();
        $this->demandeMetaRepository->flush();

        $this->mailerBottin->sendMailNewDemande($fiche);

        return ['error' => 0];
    }


}
