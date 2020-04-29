<?php

namespace AcMarche\Bottin\Controller;

use AcMarche\Bottin\Repository\UserRepository;
use AcMarche\Bottin\Service\CategoryService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Default controller.
 *
 * @IsGranted("ROLE_BOTTIN_ADMIN")
 */
class DefaultController extends AbstractController
{
    /**
     * @var CategoryService
     */
    private $categoryService;

    public function __construct(
        CategoryService $categoryService
    ) {
        $this->categoryService = $categoryService;
    }

    /**
     * @Route("/", name="bottin_home")
     */
    public function index()
    {
        return $this->render(
            '@AcMarcheBottin/default/index.html.twig'
        );
    }

    /**
     * @Route("/empty", name="bottin_categories_empty")
     */
    public function empty()
    {
        $categories = $this->categoryService->getEmpyCategories();

        return $this->render(
            '@AcMarcheBottin/default/empty.html.twig',
            [
                'categories' => $categories,
            ]
        );
    }
}
