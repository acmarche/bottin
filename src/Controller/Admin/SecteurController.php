<?php

namespace AcMarche\Bottin\Controller\Admin;

use AcMarche\Bottin\Repository\FicheRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Secteur controller.
 *
 * @IsGranted("ROLE_BOTTIN_ADMIN")
 */
#[Route(path: '/admin/secteur')]
class SecteurController extends AbstractController
{
    public function __construct(private FicheRepository $ficheRepository)
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
