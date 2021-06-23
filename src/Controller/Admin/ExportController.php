<?php

namespace AcMarche\Bottin\Controller\Admin;

use AcMarche\Bottin\Form\SelectCategoryType;
use AcMarche\Bottin\Repository\CategoryRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Export controller.
 *
 * @Route("/admin/export/select")
 * @IsGranted("ROLE_BOTTIN_ADMIN")
 */
class ExportController extends AbstractController
{
    private CategoryRepository $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @Route("/", name="bottin_admin_export_select", methods={"GET"})
     */
    public function selection(): Response
    {
        $form = $this->createForm(SelectCategoryType::class);
        $categories = $this->categoryRepository->getRootNodes();

        return $this->render(
            '@AcMarcheBottin/admin/export/index.html.twig',
            [
                'form' => $form->createView(),
                'categories' => $categories,
            ]
        );
    }

    /**
     * @Route("/add/{id}", name="bottin_admin_export_add", methods={"POST"})
     */
    public function delete(): void
    {
    }
}
