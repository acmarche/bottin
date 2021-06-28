<?php

namespace AcMarche\Bottin\Controller\Front;

use AcMarche\Bottin\Entity\Fiche;
use AcMarche\Bottin\Entity\Token;
use AcMarche\Bottin\Repository\ClassementRepository;
use AcMarche\Bottin\Repository\FicheRepository;
use AcMarche\Bottin\Service\FormUtils;
use AcMarche\Bottin\Service\HoraireService;
use AcMarche\Bottin\Utils\PathUtils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Fiche controller.
 *
 * @Route("/fiche")
 */
class FicheController extends AbstractController
{
    private FicheRepository $ficheRepository;
    private ClassementRepository $classementRepository;
    private PathUtils $pathUtils;

    public function __construct(
        PathUtils $pathUtils,
        ClassementRepository $classementRepository,
        FicheRepository $ficheRepository
    ) {
        $this->ficheRepository = $ficheRepository;
        $this->classementRepository = $classementRepository;
        $this->pathUtils = $pathUtils;
    }

    /**
     * Finds and displays a Fiche fiche.
     *
     * @Route("/{slug}", name="bottin_front_fiche_show", methods={"GET"})
     */
    public function show(Fiche $fiche): Response
    {
        $classements = $this->classementRepository->getByFiche($fiche);
        $classements = $this->pathUtils->setPathForClassements($classements);

        return $this->render(
            '@AcMarcheBottin/front/fiche/show.html.twig',
            [
                'fiche' => $fiche,
                'classements' => $classements,
            ]
        );
    }

    /**
     * @Route("/by/index/{anchor}", name="bottin_front_fiche_by_index")
     */
    public function index($anchor = null): Response
    {
        $fiches = $this->ficheRepository->findAllWithJoins();

        return $this->render(
            '@AcMarcheBottin/front/fiche/byindex.html.twig',
            [
                'fiches' => $fiches,
                'anchor' => $anchor,
            ]
        );
    }
}
