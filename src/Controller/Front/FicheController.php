<?php

namespace AcMarche\Bottin\Controller\Front;

use AcMarche\Bottin\Entity\Fiche;
use AcMarche\Bottin\Repository\ClassementRepository;
use AcMarche\Bottin\Repository\FicheRepository;
use AcMarche\Bottin\Utils\PathUtils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Fiche controller.
 */
#[Route(path: '/fiche')]
class FicheController extends AbstractController
{
    public function __construct(private PathUtils $pathUtils, private ClassementRepository $classementRepository, private FicheRepository $ficheRepository)
    {
    }

    /**
     * Finds and displays a Fiche fiche.
     */
    #[Route(path: '/{slug}', name: 'bottin_front_fiche_show', methods: ['GET'])]
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

    #[Route(path: '/by/index/{anchor}', name: 'bottin_front_fiche_by_index')]
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
