<?php


namespace AcMarche\Bottin\Service;

use AcMarche\Bottin\Entity\Demande;
use AcMarche\Bottin\Entity\DemandeMeta;
use AcMarche\Bottin\Repository\DemandeMetaRepository;
use AcMarche\Bottin\Repository\DemandeRepository;
use AcMarche\Bottin\Repository\FicheRepository;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class DemandeHandler
{
    private \AcMarche\Bottin\Repository\FicheRepository $ficheRepository;
    private \AcMarche\Bottin\Repository\DemandeRepository $demandeRepository;
    private \AcMarche\Bottin\Repository\DemandeMetaRepository $demandeMetaRepository;
    private \AcMarche\Bottin\Service\MailerBottin $mailerBottin;

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
        $idFiche = $data['id'] ?? null;
        if (!$idFiche) {
            return ['error' => 402];
        }

        $fiche = $this->ficheRepository->find($idFiche);
        if ($fiche === null) {
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

        try {
            $this->mailerBottin->sendMailNewDemande($fiche);
        } catch (TransportExceptionInterface | \Exception $e) {
            return ['error' => $e->getMessage()];
        }

        return ['error' => 0];
    }


}
