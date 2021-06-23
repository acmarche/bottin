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
 * @Route("/admin/secteur")
 * @IsGranted("ROLE_BOTTIN_ADMIN")
 */
class SecteurController extends AbstractController
{
    private FicheRepository $ficheRepository;

    public function __construct(FicheRepository $ficheRepository)
    {
        $this->ficheRepository = $ficheRepository;
    }

    /**
     * @Route("/{anchor}", name="bottin_admin_index")
     */
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
