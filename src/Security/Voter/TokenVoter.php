<?php

namespace AcMarche\Bottin\Security\Voter;

use AcMarche\Bottin\Entity\Token;
use AcMarche\Bottin\Token\TokenUtils;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class TokenVoter extends Voter
{
    private TokenUtils $tokenUtils;

    public function __construct(
        TokenUtils $tokenUtils
    ) {
        $this->tokenUtils = $tokenUtils;
    }

    protected function supports(string $attribute, $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, ['POST_EDIT', 'POST_VIEW'])
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

        switch ($attribute) {
            case 'POST_EDIT':
                // logic to determine if the user can EDIT
                // return true or false
                break;
            case 'POST_VIEW':
                // logic to determine if the user can VIEW
                // return true or false
                break;
        }

        return true;
    }
}
