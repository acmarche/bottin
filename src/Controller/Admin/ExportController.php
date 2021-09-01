<?php

namespace AcMarche\Bottin\Controller\Admin;

use AcMarche\Bottin\Entity\Selection;
use AcMarche\Bottin\Form\SelectCategoryType;
use AcMarche\Bottin\Repository\CategoryRepository;
use AcMarche\Bottin\Repository\SelectionRepository;
use AcMarche\Bottin\Utils\PathUtils;
use AcMarche\Bottin\Utils\SortUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Export controller.
 *
 * @Route("/admin/export")
 * @IsGranted("ROLE_BOTTIN_ADMIN")
 */
class ExportController extends AbstractController
{
    private CategoryRepository $categoryRepository;
    private SelectionRepository $selectionRepository;
    private PathUtils $pathUtils;

    public function __construct(
        CategoryRepository $categoryRepository,
        SelectionRepository $selectionRepository,
        PathUtils $pathUtils
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->selectionRepository = $selectionRepository;
        $this->pathUtils = $pathUtils;
    }

    /**
     * @Route("/", name="bottin_admin_export_index", methods={"GET"})
     */
    public function index(Request $request): Response
    {
        return $this->render(
            '@AcMarcheBottin/admin/export/index.html.twig',
            [
            ]
        );
    }

    /**
     * @Route("/select", name="bottin_admin_export_select", methods={"GET", "POST"})
     */
    public function selection(Request $request): Response
    {
        $form = $this->createForm(SelectCategoryType::class);
        $categories = $this->categoryRepository->getRootNodes();
        $categories = SortUtils::sortCategories($categories);

        $user = $this->getUser();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $categorySelected = $form->get('categorySelected')->getData();

            if ($categorySelected < 1) {
                $this->addFlash('danger', 'Aucune catégorie sélectionnée');

                return $this->redirectToRoute('bottin_admin_export_select');
            }

            $category = $this->categoryRepository->find($categorySelected);
            $selection = new Selection($category, $user->getUserIdentifier());
            $this->selectionRepository->persist($selection);
            $this->selectionRepository->flush();
            $this->redirectToRoute('bottin_admin_export_select');
        }

        $selections = $this->selectionRepository->findByUser($user->getUserIdentifier());

        array_map(
            function ($selection) {
                $category = $selection->getCategory();
                $selection->getCategory()->setPath($this->pathUtils->getPath($category));
            },
            $selections
        );

        return $this->render(
            '@AcMarcheBottin/admin/export/select.html.twig',
            [
                'form' => $form->createView(),
                'categories' => $categories,
                'selections' => $selections,
            ]
        );
    }

    /**
     * @Route("/add/{id}", name="bottin_admin_selection_delete", methods={"GET"})
     */
    public function delete(Selection $selection): Response
    {
        $this->selectionRepository->remove($selection);
        $this->selectionRepository->flush();
        $this->addFlash('success', 'Sélection retirée');

        return $this->redirectToRoute('bottin_admin_export_select');
    }
}
