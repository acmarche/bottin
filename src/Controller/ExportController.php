<?php

namespace AcMarche\Bottin\Controller;

use AcMarche\Bottin\Entity\Category;
use AcMarche\Bottin\Form\SelectCategoryType;
use AcMarche\Bottin\Repository\CategoryRepository;
use AcMarche\Bottin\Service\CategoryService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Export controller.
 *
 * @Route("/export/select")
 * @IsGranted("ROLE_BOTTIN_ADMIN")
 */
class ExportController extends AbstractController
{
    /**
     * @var CategoryRepository
     */
    private $categoryRepository;
    /**
     * @var CategoryService
     */
    private $categoryService;

    public function __construct(CategoryRepository $categoryRepository, CategoryService $categoryService)
    {
        $this->categoryRepository = $categoryRepository;
        $this->categoryService = $categoryService;
    }

    /**
     * @Route("/", name="bottin_export_select", methods={"GET"})
     */
    public function categoryXls(Category $category = null): Response
    {
        $form = $this->createForm(SelectCategoryType::class);
        return $this->render(
            '@AcMarcheBottin/export/index.html.twig', [

                'form' => $form->createView(),
            ]
        );
    }
}
