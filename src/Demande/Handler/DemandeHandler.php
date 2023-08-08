<?php

namespace AcMarche\Bottin\Demande\Handler;

use AcMarche\Bottin\Entity\Demande;
use AcMarche\Bottin\Entity\DemandeMeta;
use AcMarche\Bottin\Entity\Fiche;
use AcMarche\Bottin\Mailer\MailFactory;
use AcMarche\Bottin\Repository\DemandeMetaRepository;
use AcMarche\Bottin\Repository\DemandeRepository;
use AcMarche\Bottin\Repository\FicheRepository;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;

class DemandeHandler
{
    public function __construct(private readonly FicheRepository $ficheRepository, private readonly DemandeRepository $demandeRepository, private readonly DemandeMetaRepository $demandeMetaRepository, private readonly MailFactory $mailFactory, private readonly MailerInterface $mailer)
    {
    }

    public function handle(array $data): array
    {
        $idFiche = $data['id'] ?? null;
        if (!$idFiche) {
            return ['error' => 402];
        }

        $fiche = $this->ficheRepository->find($idFiche);
        if (!$fiche instanceof Fiche) {
            return ['error' => 404];
        }

        $demande = new Demande();
        $demande->setFiche($fiche);

        $this->demandeRepository->persist($demande);
        foreach ($data as $key => $value) {
            if ('id' == $key) {
                continue;
            }

            $demandeMeta = new DemandeMeta($demande, $key, $value);
            $demande->addMeta($demandeMeta);
            $this->demandeMetaRepository->persist($demandeMeta);
        }

        $this->demandeRepository->flush();
        $this->demandeMetaRepository->flush();
        $email = $this->mailFactory->mailNewDemande($fiche);
        try {
            $this->mailer->send($email);

            return ['error' => 0];
        } catch (TransportExceptionInterface $transportException) {
            return ['error' => $transportException->getMessage()];
        }
    }
}
