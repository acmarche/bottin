<?php

namespace AcMarche\Bottin\Security;

use AcMarche\Bottin\Parameter\Option;
use AcMarche\Bottin\Repository\UserRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Ldap\Security\LdapBadge;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

/**
 * Essayer de voir les events.
 *
 * @see UserCheckerListener::postCheckCredentials
 * @see UserProviderListener::checkPassport
 * @see CheckCredentialsListener
 * @see CheckLdapCredentialsListener
 * bin/console debug:event-dispatcher --dispatcher=security.event_dispatcher.main
 */
class BottinLdapAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    final public const LOGIN_ROUTE = 'app_login';

    public function __construct(
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly UserRepository $userRepository,
        private readonly ParameterBagInterface $parameterBag
    ) {
    }

    public function authenticate(Request $request): Passport
    {
        $email = $request->request->get('username', '');
        $password = $request->request->get('password', '');
        $token = $request->request->get('_csrf_token', '');

        $request->getSession()->set(SecurityRequestAttributes::LAST_USERNAME, $email);

        $badges =
            [
                new CsrfTokenBadge('authenticate', $token),
            ];

        $query = sprintf('(&(|(sAMAccountName=*%s*))(objectClass=person))', $email);
        $badges[] = new LdapBadge(
            LdapBottin::class,
            $this->parameterBag->get(Option::LDAP_DN),
            $this->parameterBag->get(Option::LDAP_USER),
            $this->parameterBag->get(Option::LDAP_PASSWORD),
            $query
        );

        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($password),
            $badges
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        return new RedirectResponse($this->urlGenerator->generate('bottin_front_home'));
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
