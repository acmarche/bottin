<?php

namespace AcMarche\Bottin\Controller;

use AcMarche\Bottin\Category\Message\CategoryCreated;
use AcMarche\Bottin\Category\Message\CategoryDeleted;
use AcMarche\Bottin\Category\Message\CategoryUpdated;
use AcMarche\Bottin\Entity\Category;
use AcMarche\Bottin\Form\CategoryMoveType;
use AcMarche\Bottin\Form\CategoryType;
use AcMarche\Bottin\Form\Search\SearchCategoryType;
use AcMarche\Bottin\Repository\CategoryRepository;
use AcMarche\Bottin\Service\CategoryService;
use AcMarche\Bottin\Utils\PathUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Category controller.
 *
 * @Route("/category")
 * @IsGranted("ROLE_BOTTIN_ADMIN")
 */
class CategoryController extends AbstractController
{
    /**
     * @var CategoryRepository
     */
    private $categoryRepository;
    /**
     * @var CategoryService
     */
    private $categoryService;
    /**
     * @var PathUtils
     */
    private $pathUtils;

    public function __construct(
        CategoryRepository $categoryRepository,
        CategoryService $categoryService,
        PathUtils $pathUtils
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->categoryService = $categoryService;
        $this->pathUtils = $pathUtils;
    }

    /**
     * Lists all Category entities.
     *
     * @Route("/", name="bottin_category", methods={"GET"})
     */
    public function index(Request $request)
    {
        $session = $request->getSession();

        $args = $data = [];
        $categoryRoot = null;

        if ($session->has('category_search')) {
            $args = json_decode($session->get('category_search'), true);
        }

        $search_form = $this->createForm(
            SearchCategoryType::class,
            $args,
            [
                'method' => 'GET',
            ]
        );

        $search_form->handleRequest($request);

        if ($search_form->isSubmitted() && $search_form->isValid()) {
            $args = $search_form->getData();
            $name = $args['name'];
            $root = $args['parent'] ?? false;

            if ($name) {
                $args['name'] = $name;
                $args['parent'] = $root;
            }

            $session->set('category_search', json_encode($args));

            if ($search_form->get('raz')->isClicked()) {
                $session->remove('category_search');
                $this->addFlash('info', 'La recherche a bien été réinitialisée.');

                return $this->redirectToRoute('bottin_category');
            }

            if ($root) {
                $categoryRoot = $this->categoryRepository->find($root);
            }

            $categories = $this->categoryRepository->search($name, $categoryRoot);
        } else {
            $categories = $this->categoryRepository->getRootNodes();
        }

        foreach ($categories as $rootNode) {
            $data[] = $this->categoryRepository->getTree($rootNode->getRealMaterializedPath());
        }

        return $this->render(
            '@AcMarcheBottin/category/index.html.twig',
            [
                'search_form' => $search_form->createView(),
                'categories' => $data,
            ]
        );
    }

    /**
     * Displays a form to create a new Category entity.
     *
     * @Route("/new", name="bottin_category_new")
     * @Route("/new/{id}", name="bottin_category_new_children", methods={"GET", "POST"})
     */
    public function new(Request $request, Category $parent = null)
    {
        $category = new Category();

        if ($parent) {
            $category->setParent($parent);
        }

        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->categoryRepository->persist($category);
            $this->categoryRepository->flush();

            if ($parent) {
                $category->setChildNodeOf($parent);
                $this->categoryRepository->flush();
            }

            $this->dispatchMessage(new CategoryCreated($category->getId()));

            $this->addFlash('success', 'La catégorie a bien été crée');

            return $this->redirectToRoute('bottin_category_show', ['id' => $category->getId()]);
        }

        return $this->render(
            '@AcMarcheBottin/category/new.html.twig',
            [
                'parent' => $parent,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * Finds and displays a Category entity.
     *
     * @Route("/{id}", name="bottin_category_show", methods={"GET"})
     */
    public function show(Category $category)
    {
        $paths = $this->pathUtils->getPath($category);
        /**
         * get all fiches of this category and there children.
         */
        $fiches = $this->categoryService->getFichesByCategoryAndHerChildren($category);

        $category->getMaterializedPath();//1/2
        $category->getRealMaterializedPath();//1/2/3
        $category->getRootMaterializedPath();//1

        $category = $this->categoryRepository->getTree($category->getRealMaterializedPath());

        return $this->render(
            '@AcMarcheBottin/category/show.html.twig',
            [
                'category' => $category,
                'paths' => $paths,
                'fiches' => $fiches,
            ]
        );
    }

    /**
     * Displays a form to edit an existing Category entity.
     *
     * @Route("/{id}/edit", name="bottin_category_edit", methods={"GET", "POST"})
     */
    public function edit(Category $category, Request $request)
    {
        $editForm = $this->createForm(CategoryType::class, $category);

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->categoryRepository->flush();

            $this->dispatchMessage(new CategoryUpdated($category->getId()));

            return $this->redirectToRoute('bottin_category_show', ['id' => $category->getId()]);
        }

        return $this->render(
            '@AcMarcheBottin/category/edit.html.twig',
            [
                'category' => $category,
                'form' => $editForm->createView(),
            ]
        );
    }

    /**
     * Displays a form to edit an existing Category entity.
     *
     * @Route("/{id}/move", name="bottin_category_move", methods={"GET", "POST"})
     */
    public function move(Category $category, Request $request)
    {
        $editForm = $this->createForm(CategoryMoveType::class, $category);

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $category->setChildNodeOf($category->getParent());

            $this->categoryRepository->flush();
            $this->addFlash('success', 'La catégorie a bien été modifiée');

            return $this->redirectToRoute('bottin_category_show', ['id' => $category->getId()]);
        }

        return $this->render(
            '@AcMarcheBottin/category/move.html.twig',
            [
                'category' => $category,
                'form' => $editForm->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}", name="bottin_category_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Category $category): Response
    {
        if ($this->isCsrfTokenValid('delete'.$category->getId(), $request->request->get('_token'))) {
            $this->dispatchMessage(new CategoryDeleted($category->getId()));
            $parent = $category->getParent();
            $this->categoryRepository->remove($category);
            $this->categoryRepository->flush();

            $this->addFlash('success', "La catégorie a bien été supprimée");
            if ($parent) {
                return $this->redirect(
                    $this->generateUrl('bottin_category_show', ['id' => $parent->getId()])
                );
            }
        }

        return $this->redirect($this->generateUrl('bottin_category'));
    }
}
