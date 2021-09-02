<?php

namespace AcMarche\Bottin\Twig;

use AcMarche\Bottin\Entity\Fiche;
use AcMarche\Bottin\Repository\TokenRepository;
use Elastica\Result;
use Elastica\ResultSet;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Security;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class BottinExtension extends AbstractExtension
{
    private Security $security;
    private RouterInterface $router;
    private RequestStack $requestStack;
    private TokenRepository $tokenRepository;

    public function __construct(
        Security $security,
        RouterInterface $router,
        RequestStack $requestStack,
        TokenRepository $tokenRepository
    ) {
        $this->security = $security;
        $this->router = $router;
        $this->requestStack = $requestStack;
        $this->tokenRepository = $tokenRepository;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('bottin_url_fiche_show', [$this, 'urlFiche']),
        ];
    }

    public function urlFiche($fiche): string
    {
        if ($token = $this->requestStack->getCurrentRequest()->get('token')) {
            return $this->router->generate('bottin_backend_fiche_show', ['uuid' => $token->getUuid()]);
        }

        if (\is_array($fiche)) {
            $id = $fiche['id'];
            $slug = $fiche['slug'];
        }

        if ($fiche instanceof Result) {
            $source = $fiche->getSource();
            $id = $source['id'];
            $slug = $source['slug'];
        }

        if ($fiche instanceof Fiche) {
            $id = $fiche->getId();
            $slug = $fiche->getSlug();
        }

        if ($this->security->isGranted('ROLE_BOTTIN_ADMIN')) {
            return $this->router->generate('bottin_admin_fiche_show', ['id' => $id]);
        }

        return $this->router->generate('bottin_front_fiche_show', ['slug' => $slug]);
    }
}
