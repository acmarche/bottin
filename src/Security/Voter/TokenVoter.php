<?php

namespace AcMarche\Bottin\Security\Voter;

use AcMarche\Bottin\Entity\Fiche;
use AcMarche\Bottin\Entity\Token;
use AcMarche\Bottin\Token\TokenUtils;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class TokenVoter extends Voter
{
    final public const TOKEN_EDIT = 'TOKEN_EDIT';

    public function __construct(private readonly TokenUtils $tokenUtils)
    {
    }

    protected function supports(string $attribute, $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return self::TOKEN_EDIT == $attribute
            && $subject instanceof Token;
    }

    /**
     * @param Token $subject
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        if (!$subject->fiche instanceof Fiche) {
            return false;
        }

        return !$this->tokenUtils->isExpired($subject);
    }
}
