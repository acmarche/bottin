<?php

namespace AcMarche\Bottin\Controller\Admin;

use AcMarche\Bottin\Category\Form\CategoryMoveType;
use AcMarche\Bottin\Category\Form\CategoryType;
use AcMarche\Bottin\Category\Message\CategoryCreated;
use AcMarche\Bottin\Category\Message\CategoryDeleted;
use AcMarche\Bottin\Category\Message\CategoryUpdated;
use AcMarche\Bottin\Category\Repository\CategoryService;
use AcMarche\Bottin\Entity\Category;
use AcMarche\Bottin\Form\Search\SearchCategoryType;
use AcMarche\Bottin\Repository\CategoryRepository;
use AcMarche\Bottin\Utils\PathUtils;
use AcMarche\Bottin\Utils\SortUtils;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Category controller.
 */
#[Route(path: '/admin/category')]
#[IsGranted('ROLE_BOTTIN_ADMIN')]
class CategoryController extends AbstractController
{
    public function __construct(
        private readonly CategoryRepository $categoryRepository,
        private readonly CategoryService $categoryService,
        private readonly PathUtils $pathUtils,
        private readonly MessageBusInterface $messageBus
    ) {
    }

    /**
     * Lists all Category entities.
     */
    #[Route(path: '/', name: 'bottin_admin_category', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $session = $request->getSession();
        $args = [];
        $data = [];
        $categoryRoot = null;
        if ($session->has('category_search')) {
            $args = json_decode((string) $session->get('category_search'), true, 512, JSON_THROW_ON_ERROR);
        }

        $form = $this->createForm(
            SearchCategoryType::class,
            $args,
            [
                'method' => 'GET',
            ]
        );
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $args = $form->getData();
            $name = $args['name'];
            $root = $args['parent'] ?? false;

            if ($name) {
                $args['name'] = $name;
                $args['parent'] = $root;
            }

            $session->set('category_search', json_encode($args, JSON_THROW_ON_ERROR));

            if ($root) {
                $categoryRoot = $this->categoryRepository->find($root);
            }

            $categories = $this->categoryRepository->search($name, $categoryRoot);
        } else {
            $categories = $this->categoryRepository->getRootNodes();
            $categories = SortUtils::sortCategories($categories);
        }

        foreach ($categories as $rootNode) {
            $data[] = $this->categoryRepository->getTree($rootNode->getRealMaterializedPath());
        }

        return $this->render(
            '@AcMarcheBottin/admin/category/index.html.twig',
            [
                'search_form' => $form->createView(),
                'categories' => $data,
            ]
        );
    }

    /**
     * Displays a form to create a new Category entity.
     */
    #[Route(path: '/new', name: 'bottin_admin_category_new')]
    #[Route(path: '/new/{id}', name: 'bottin_admin_category_new_children', methods: ['GET', 'POST'])]
    public function new(Request $request, Category $parent = null): Response
    {
        $category = new Category();
        if ($parent instanceof Category) {
            $category->setParent($parent);
        }

        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->categoryRepository->persist($category);
            $this->categoryRepository->flush();

            if ($parent instanceof Category) {
                $category->setChildNodeOf($parent);
                $this->categoryRepository->flush();
            }

            $this->messageBus->dispatch(new CategoryCreated($category->getId()));

            return $this->redirectToRoute('bottin_admin_category_show', ['id' => $category->getId()]);
        }

        return $this->render(
            '@AcMarcheBottin/admin/category/new.html.twig',
            [
                'parent' => $parent,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * Finds and displays a Category entity.
     */
    #[Route(path: '/{id}', name: 'bottin_admin_category_show', methods: ['GET'])]
    public function show(Category $category): Response
    {
        $paths = $this->pathUtils->getPath($category);
        /**
         * get all fiches of this category and there children.
         */
        $fiches = $this->categoryService->getFichesByCategoryAndHerChildren($category);
        $category->getMaterializedPath();
        //1/2
        $category->getRealMaterializedPath();
        //1/2/3
        $category->getRootMaterializedPath();
        //1
        $category = $this->categoryRepository->getTree($category->getRealMaterializedPath());

        return $this->render(
            '@AcMarcheBottin/admin/category/show.html.twig',
            [
                'category' => $category,
                'paths' => $paths,
                'fiches' => $fiches,
            ]
        );
    }

    /**
     * Displays a form to edit an existing Category entity.
     */
    #[Route(path: '/{id}/edit', name: 'bottin_admin_category_edit', methods: ['GET', 'POST'])]
    public function edit(Category $category, Request $request): Response
    {
        $editForm = $this->createForm(CategoryType::class, $category);
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->categoryRepository->flush();

            $this->messageBus->dispatch(new CategoryUpdated($category->getId()));

            return $this->redirectToRoute('bottin_admin_category_show', ['id' => $category->getId()]);
        }

        return $this->render(
            '@AcMarcheBottin/admin/category/edit.html.twig',
            [
                'category' => $category,
                'form' => $editForm->createView(),
            ]
        );
    }

    /**
     * Displays a form to edit an existing Category entity.
     */
    #[Route(path: '/{id}/move', name: 'bottin_admin_category_move', methods: ['GET', 'POST'])]
    public function move(Category $category, Request $request): Response
    {
        $editForm = $this->createForm(CategoryMoveType::class, $category);
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $category->setChildNodeOf($category->getParent());

            $this->categoryRepository->flush();
            $this->addFlash('success', 'La catégorie a bien été modifiée');

            return $this->redirectToRoute('bottin_admin_category_show', ['id' => $category->getId()]);
        }

        return $this->render(
            '@AcMarcheBottin/admin/category/move.html.twig',
            [
                'category' => $category,
                'form' => $editForm->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'bottin_admin_category_delete', methods: ['POST'])]
    public function delete(Request $request, Category $category): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete'.$category->getId(), $request->request->get('_token'))) {
            $this->messageBus->dispatch(new CategoryDeleted($category->getId()));
            $parent = $category->getParent();
            $this->categoryRepository->remove($category);
            $this->categoryRepository->flush();

            $this->addFlash('success', 'La catégorie a bien été supprimée');
            if ($parent instanceof Category) {
                return $this->redirectToRoute('bottin_admin_category_show', ['id' => $parent->getId()]);
            }
        }

        return $this->redirectToRoute('bottin_admin_category');
    }
}
