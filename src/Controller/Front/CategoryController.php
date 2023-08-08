<?php

namespace AcMarche\Bottin\Controller\Front;

use AcMarche\Bottin\Category\Repository\CategoryService;
use AcMarche\Bottin\Entity\Category;
use AcMarche\Bottin\Repository\CategoryRepository;
use AcMarche\Bottin\Utils\PathUtils;
use AcMarche\Bottin\Utils\SortUtils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Category controller.
 */
#[Route(path: '/category')]
class CategoryController extends AbstractController
{
    public function __construct(private readonly CategoryRepository $categoryRepository, private readonly CategoryService $categoryService, private readonly PathUtils $pathUtils)
    {
    }

    /**
     * Lists all Category entities.
     */
    #[Route(path: '/', name: 'bottin_front_category_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $categories = $this->categoryRepository->getRootNodes();
        $categories = SortUtils::sortCategories($categories);
        foreach ($categories as $rootNode) {
            $data[] = $this->categoryRepository->getTree($rootNode->getRealMaterializedPath());
        }

        return $this->render(
            '@AcMarcheBottin/front/category/index.html.twig',
            [
                'categories' => $data,
            ]
        );
    }

    /**
     * Finds and displays a Category entity.
     */
    #[Route(path: '/{slug}', name: 'bottin_front_category_show', methods: ['GET'])]
    public function show(Category $category): Response
    {
        $paths = $this->pathUtils->getPath($category);
        /**
         * get all fiches of this category and there children.
         */
        $fiches = $this->categoryService->getFichesByCategoryIdWithOutChildrend($category);
        $category->getMaterializedPath();
        //1/2
        $category->getRealMaterializedPath();
        //1/2/3
        $category->getRootMaterializedPath();
        //1
        $category = $this->categoryRepository->getTree($category->getRealMaterializedPath());

        return $this->render(
            '@AcMarcheBottin/front/category/show.html.twig',
            [
                'category' => $category,
                'paths' => $paths,
                'fiches' => $fiches,
            ]
        );
    }
}
