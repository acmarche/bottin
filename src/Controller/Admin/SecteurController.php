<?php

namespace AcMarche\Bottin\Controller\Admin;

use AcMarche\Bottin\Repository\FicheRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Secteur controller.
 */
#[Route(path: '/admin/secteur')]
#[IsGranted('ROLE_BOTTIN_ADMIN')]
class SecteurController extends AbstractController
{
    public function __construct(private readonly FicheRepository $ficheRepository)
    {
    }

    #[Route(path: '/{anchor}', name: 'bottin_admin_index')]
    public function index($anchor = null): Response
    {
        $fiches = $this->ficheRepository->findAllWithJoins();

        return $this->render(
            '@AcMarcheBottin/admin/secteur/index.html.twig',
            [
                'fiches' => $fiches,
                'anchor' => $anchor,
            ]
        );
    }
}
