<?php

namespace AcMarche\Bottin\Twig;

use AcMarche\Bottin\Entity\Fiche;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Security;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class BottinExtension extends AbstractExtension
{
    private Security $security;
    private RouterInterface $router;

    public function __construct(Security $security, RouterInterface $router)
    {
        $this->security = $security;
        $this->router = $router;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('bottin_url_fiche_show', [$this, 'urlFiche']),
        ];
    }

    public function urlFiche($fiche): string
    {
        if (is_array($fiche)) {
            $id = $fiche['id'];
            $slug = $fiche['slug'];
        } else {
            $id = $fiche->getId();
            $slug = $fiche->getSlug();
        }

        if ($this->security->isGranted('ROLE_BOTTIN_ADMIN')) {
            return $this->router->generate('bottin_admin_fiche_show', ['id' => $id]);
        }

        return $this->router->generate('bottin_front_fiche_show', ['slug' => $slug]);
    }
}
