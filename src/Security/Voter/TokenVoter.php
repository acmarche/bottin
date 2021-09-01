<?php

namespace AcMarche\Bottin\Security\Voter;

use AcMarche\Bottin\Entity\Token;
use AcMarche\Bottin\Token\TokenUtils;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class TokenVoter extends Voter
{
    private TokenUtils $tokenUtils;
    public const TOKEN_EDIT = 'TOKEN_EDIT';

    public function __construct(
        TokenUtils $tokenUtils
    ) {
        $this->tokenUtils = $tokenUtils;
    }

    protected function supports(string $attribute, $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return \in_array($attribute, [self::TOKEN_EDIT])
            && $subject instanceof Token;
    }

    /**
     * @param Token $subject
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        if (!$subject->getFiche()) {
            return false;
        }
        if ($this->tokenUtils->isExpired($subject)) {
            return false;
        }

        return true;
    }
}
