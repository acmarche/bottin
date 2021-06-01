<?php

namespace AcMarche\Bottin\Controller;

use AcMarche\Bottin\Service\CategoryService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class CheckupController extends AbstractController
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
     * @Route("/empty", name="bottin_categories_empty")
     * @IsGranted("ROLE_BOTTIN_ADMIN")
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
