<?php

namespace AcMarche\Bottin\Controller\Admin;

use AcMarche\Bottin\Service\CategoryService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CheckupController extends AbstractController
{
    private CategoryService $categoryService;

    public function __construct(
        CategoryService $categoryService
    ) {
        $this->categoryService = $categoryService;
    }

    /**
     * @Route("/empty", name="bottin_categories_empty")
     * @IsGranted("ROLE_BOTTIN_ADMIN")
     */
    public function empty(): Response
    {
        $categories = $this->categoryService->getEmpyCategories();

        return $this->render(
            '@AcMarcheBottin/admin/default/empty.html.twig',
            [
                'categories' => $categories,
            ]
        );
    }


}
