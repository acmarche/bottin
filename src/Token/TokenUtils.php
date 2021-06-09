<?php

namespace AcMarche\Bottin\Token;

use AcMarche\Bottin\Entity\Fiche;
use AcMarche\Bottin\Entity\Token;
use AcMarche\Bottin\Repository\FicheRepository;
use AcMarche\Bottin\Repository\TokenRepository;
use DateTime;

class TokenUtils
{
    private FicheRepository $ficheRepository;
    private TokenRepository $tokenRepository;

    public function __construct(
        FicheRepository $ficheRepository,
        TokenRepository $tokenRepository
    ) {
        $this->ficheRepository = $ficheRepository;
        $this->tokenRepository = $tokenRepository;
    }

    public function generateForAll()
    {
        $fiches = $this->ficheRepository->findAllWithJoins();

        foreach ($fiches as $fiche) {
            $this->generateForOneFiche($fiche);
        }

        $this->tokenRepository->flush();
    }

    public function generateForOneFiche(Fiche $fiche)
    {
        if (!$token = $fiche->getToken()) {
            $token = new Token($fiche);
            $this->tokenRepository->persist($token);
        }
        $token->setUuid($token->generateUuid());
        $date = new DateTime();
        $date->modify('+30days');
        $token->setExpireAt($date);
    }

    public function isExpired(Token $token): bool
    {
        $today = new \DateTime();

        return $token->getCreatedAt()->format('Y-m-d') > $today->format('Y-m-d');
    }
}
