<?php

namespace AcMarche\Bottin\Controller\Admin;

use AcMarche\Bottin\Classement\Message\ClassementDeleted;
use AcMarche\Bottin\Entity\Classement;
use AcMarche\Bottin\Repository\CategoryRepository;
use AcMarche\Bottin\Repository\ClassementRepository;
use AcMarche\Bottin\Utils\PathUtils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Ajax controller.
 *
 * @todo protect
 */
#[Route(path: '/admin/ajax')]
class AjaxController extends AbstractController
{
    public function __construct(
        private readonly PathUtils $pathUtils,
        private readonly ClassementRepository $classementRepository,
        private readonly CategoryRepository $categoryRepository,
        private readonly MessageBusInterface $messageBus
    ) {
    }

    #[Route(path: '/removeclassment', name: 'bottin_admin_ajax_remove_classement', methods: ['POST'])]
    public function removeClassement(Request $request): Response
    {
        $classementId = (int)$request->get('classementId');
        $classement = $this->classementRepository->find($classementId);
        if (!$classement instanceof Classement) {
            $error = 'classement non trouvé';
            $template = $this->renderView('@AcMarcheBottin/admin/ajax/error.html.twig', ['error' => $error]);

            return new Response($template);
        }

        $fiche = $classement->fiche;
        $category = $classement->category;
        $this->classementRepository->remove($classement);
        $this->classementRepository->flush();

        $this->messageBus->dispatch(new ClassementDeleted($fiche->getId(), $classementId, $category->getId()));
        $classements = $this->classementRepository->getByFiche($fiche);
        $classements = $this->pathUtils->setPathForClassements($classements);

        $template = $this->renderView(
            '@AcMarcheBottin/backend/classement/_list.html.twig',
            ['classements' => $classements]
        );

        return new Response($template);
    }

    #[Route(path: '/setprincipalclassement', name: 'bottin_admin_ajax_principal_classement', methods: ['POST'])]
    public function setPrincipal(Request $request): Response
    {
        $classementId = (int)$request->get('classementId');
        $classementSelect = $this->classementRepository->find($classementId);
        if (!$classementSelect instanceof Classement) {
            $error = 'classement non trouvé';
            $template = $this->renderView('@AcMarcheBottin/admin/ajax/error.html.twig', ['error' => $error]);
        } else {
            $fiche = $classementSelect->fiche;

            $classements = $fiche->getClassements();

            foreach ($classements as $classement) {
                if ($classement->getId() === $classementSelect->getId()) {
                    $classement->principal = true;
                } else {
                    $classement->principal = false;
                }
            }

            $this->classementRepository->flush();

            $classements = $this->classementRepository->getByFiche($fiche);
            $classements = $this->pathUtils->setPathForClassements($classements);

            $template = $this->renderView(
                '@AcMarcheBottin/backend/classement/_list.html.twig',
                ['classements' => $classements]
            );
        }

        return new Response($template);
    }

    #[Route(path: '/getcategories', name: 'bottin_admin_ajax_get_categories', methods: ['GET'])]
    public function ajaxCategories(Request $request): Response
    {
        $keyword = $request->get('q', null);
        if (!$keyword) {
            return new Response('Oups pas su obtenir les catégories');
        }

        $categories = $this->categoryRepository->search($keyword);

        return $this->render(
            '@AcMarcheBottin/admin/classement/_ajaxCategories.html.twig',
            [
                'categories' => $categories,
            ]
        );
    }

    #[Route(path: '/getcategoriesforexport', name: 'bottin_admin_ajax_get_categories_for_export', methods: ['POST'])]
    public function ajaxCategoriesForExport(Request $request): Response
    {
        $jsonResponse = new JsonResponse();
        $parentId = (int)$request->get('parentId');
        $level = (int)$request->get('level');
        ++$level;
        if (0 === $parentId) {
            $jsonResponse->setData(['error' => 'Oups pas su obtenir les catégories']);

            return $jsonResponse;
        }

        $categories = $this->categoryRepository->findBy(['parent' => $parentId], ['name' => 'ASC']);

        return $this->render(
            '@AcMarcheBottin/admin/classement/_ajaxCategoriesForExport.html.twig',
            [
                'categories' => $categories,
                'level' => $level,
            ]
        );
    }

    #[Route(path: '/fetch/{query}', name: 'bottin_admin_fetch')]
    public function fetchCategorie(string $query = null): JsonResponse
    {
        $data = [];
        $i = 0;
        foreach ($this->categoryRepository->search($query) as $category) {
            $data[$i]['id'] = $category->getId();
            $data[$i]['name'] = $category->name;
            $data[$i]['label'] = $category->name;
            $data[$i]['value'] = $category->name;
            ++$i;
        }

        return new JsonResponse($data);
    }

    #[Route(path: '/getcategory', name: 'bottin_ajax_fetch_category')]
    public function fetchCategory(Request $request): Response
    {
        $categoryId = (int)$request->get('id');
        $category = $this->categoryRepository->find($categoryId);

        return new Response($category->name);
    }
}
