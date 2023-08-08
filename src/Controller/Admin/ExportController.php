<?php

namespace AcMarche\Bottin\Controller\Admin;

use AcMarche\Bottin\Entity\Selection;
use AcMarche\Bottin\Export\ExportUtils;
use AcMarche\Bottin\Form\SelectCategoryType;
use AcMarche\Bottin\Repository\CategoryRepository;
use AcMarche\Bottin\Repository\SelectionRepository;
use AcMarche\Bottin\Utils\PathUtils;
use AcMarche\Bottin\Utils\SortUtils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Export controller.
 */
#[Route(path: '/admin/export')]
#[IsGranted('ROLE_BOTTIN_ADMIN')]
class ExportController extends AbstractController
{
    public function __construct(private readonly CategoryRepository $categoryRepository, private readonly SelectionRepository $selectionRepository, private readonly PathUtils $pathUtils, private readonly ExportUtils $exportUtils)
    {
    }

    #[Route(path: '/', name: 'bottin_admin_export_index', methods: ['GET'])]
    public function index(): Response
    {
        $user = $this->getUser();
        $fiches = $this->exportUtils->getFichesBySelection($user->getUserIdentifier());

        return $this->render(
            '@AcMarcheBottin/admin/export/index.html.twig',
            [
                'fiches' => $fiches,
            ]
        );
    }

    #[Route(path: '/select', name: 'bottin_admin_export_select', methods: ['GET', 'POST'])]
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

    #[Route(path: '/add/{id}', name: 'bottin_admin_selection_delete', methods: ['GET'])]
    public function delete(Selection $selection): RedirectResponse
    {
        $this->selectionRepository->remove($selection);
        $this->selectionRepository->flush();
        $this->addFlash('success', 'Sélection retirée');

        return $this->redirectToRoute('bottin_admin_export_select');
    }
}
