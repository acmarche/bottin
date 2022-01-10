<?php

namespace AcMarche\Bottin\Token;

use AcMarche\Bottin\Entity\Fiche;
use AcMarche\Bottin\Entity\Token;
use AcMarche\Bottin\Repository\FicheRepository;
use AcMarche\Bottin\Repository\TokenRepository;
use DateTime;

class TokenUtils
{
    public function __construct(private FicheRepository $ficheRepository, private TokenRepository $tokenRepository)
    {
    }

    public function generateForAll(): void
    {
        $fiches = $this->ficheRepository->findAllWithJoins();

        foreach ($fiches as $fiche) {
            $this->generateForOneFiche($fiche);
        }

        $this->tokenRepository->flush();
    }

    public function generateForOneFiche(Fiche $fiche, bool $flush = false): void
    {
        if (($token = $fiche->getToken()) === null) {
            $token = new Token($fiche);
            $this->tokenRepository->persist($token);
        }
        $token->setUuid($token->generateUuid());
        $token->setPassword($this->generatePassword());
        $date = new DateTime();
        $date->modify('+30days');
        $token->setExpireAt($date);
        if ($flush) {
            $this->tokenRepository->flush();
        }
    }

    public function isExpired(Token $token): bool
    {
        $today = new \DateTime();

        return $token->getCreatedAt()->format('Y-m-d') > $today->format('Y-m-d');
    }

    public function generatePassword(int $length = 6): string
    {
        $keyspace = '123456789ABCDEFGHJKLMNPQRSTUVWXYZ';
        $pieces = [];
        $max = mb_strlen($keyspace, '8bit') - 1;
        for ($i = 0; $i < $length; ++$i) {
            $pieces[] = $keyspace[random_int(0, $max)];
        }
        $password = implode('', $pieces);
        if (null !== $this->tokenRepository->findOneBy(['password' => $password])) {
            $this->generatePassword();
        }

        return $password;
    }
}
