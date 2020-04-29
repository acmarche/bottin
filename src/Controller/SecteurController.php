<?php

namespace AcMarche\Bottin\Controller;

use AcMarche\Bottin\Repository\CategoryRepository;
use AcMarche\Bottin\Repository\FicheRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Secteur controller.
 *
 * @Route("/secteur")
 * @IsGranted("ROLE_BOTTIN_ADMIN")
 */
class SecteurController extends AbstractController
{
    /**
     * @var CategoryRepository
     */
    private $categoryRepository;
    /**
     * @var FicheRepository
     */
    private $ficheRepository;

    public function __construct(CategoryRepository $categoryRepository, FicheRepository $ficheRepository)
    {
        $this->categoryRepository = $categoryRepository;
        $this->ficheRepository = $ficheRepository;
    }

    /**
     * @Route("/{anchor}", name="bottin_index")
     */
    public function index($anchor = null)
    {
        $fiches = $this->ficheRepository->search([]);

        return $this->render(
            '@AcMarcheBottin/secteur/index.html.twig',
            [
                'fiches' => $fiches,
                'anchor' => $anchor,
            ]
        );
    }
}
