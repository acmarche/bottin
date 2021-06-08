<?php

namespace AcMarche\Bottin\Controller;

use AcMarche\Bottin\Repository\CategoryRepository;
use AcMarche\Bottin\Utils\SortUtils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    private CategoryRepository $categoryRepository;

    public function __construct(
        CategoryRepository $categoryRepository
    ) {
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
}
