<?php

namespace AcMarche\Bottin\Controller;

use AcMarche\Bottin\Repository\CategoryRepository;
use AcMarche\Bottin\Repository\FicheRepository;
use AcMarche\Bottin\Utils\SortUtils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    private FicheRepository $ficheRepository;
    private CategoryRepository $categoryRepository;

    public function __construct(
        FicheRepository $ficheRepository,
        CategoryRepository $categoryRepository
    ) {
        $this->ficheRepository = $ficheRepository;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @Route("/", name="bottin_home")
     */
    public function index(): Response
    {
        $categories = $this->categoryRepository->getRootNodes();
        $categories = SortUtils::sortCategories($categories);
        foreach ($categories as $rootNode) {
            $data[] = $this->categoryRepository->getTree($rootNode->getRealMaterializedPath());
        }

        return $this->render(
            '@AcMarcheBottin/default/index.html.twig',
            [
                'categories' => $data,
            ]
        );
    }

    /**
     * @Route("/uuid", name="bottin_uuid")
     */
    public function uuid(): Response
    {
        $fiches = $this->ficheRepository->findAllWithJoins();
        foreach ($fiches as $fiche) {
            $fiche->setUuid($fiche->generateUuid());
        }

        return $this->render(
            '@AcMarcheBottin/default/uuid.html.twig',
            ['fiches' => $fiches]
        );
    }
}
