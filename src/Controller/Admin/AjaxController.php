<?php

namespace AcMarche\Bottin\Controller\Admin;

use AcMarche\Bottin\Classement\Message\ClassementDeleted;
use AcMarche\Bottin\Repository\CategoryRepository;
use AcMarche\Bottin\Repository\ClassementRepository;
use AcMarche\Bottin\Utils\PathUtils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Ajax controller.
 *
 * @Route("/admin/ajax")
 * IsGranted("ROLE_BOTTIN_ADMIN")
 * @todo protect
 */
class AjaxController extends AbstractController
{
    private ClassementRepository $classementRepository;
    private CategoryRepository $categoryRepository;
    private PathUtils $pathUtils;

    public function __construct(
        PathUtils $pathUtils,
        ClassementRepository $classementRepository,
        CategoryRepository $categoryRepository
    ) {
        $this->classementRepository = $classementRepository;
        $this->categoryRepository = $categoryRepository;
        $this->pathUtils = $pathUtils;
    }

    /**
     * @Route("/removeclassment", name="bottin_admin_ajax_remove_classement", methods={"POST"})
     */
    public function removeClassement(Request $request): Response
    {
        $classementId = (int) $request->get('classementId');
        $classement = $this->classementRepository->find($classementId);

        if (null === $classement) {
            $error = 'classement non trouvé';
            $template = $this->renderView('@AcMarcheBottin/admin/ajax/error.html.twig', ['error' => $error]);

            return new Response($template);
        }

        $fiche = $classement->getFiche();
        $category = $classement->getCategory();
        $this->classementRepository->remove($classement);
        $this->classementRepository->flush();

        $this->dispatchMessage(new ClassementDeleted($fiche->getId(), $classementId, $category->getId()));

        $classements = $this->classementRepository->getByFiche($fiche);
        $classements = $this->pathUtils->setPathForClassements($classements);

        $template = $this->renderView(
            '@AcMarcheBottin/backend/classement/_list.html.twig',
            ['classements' => $classements]
        );

        return new Response($template);
    }

    /**
     * @Route("/setprincipalclassement", name="bottin_admin_ajax_principal_classement", methods={"POST"})
     */
    public function setPrincipal(Request $request): Response
    {
        $classementId = (int) $request->get('classementId');
        $classementSelect = $this->classementRepository->find($classementId);

        if (null === $classementSelect) {
            $error = 'classement non trouvé';
            $template = $this->renderView('@AcMarcheBottin/admin/ajax/error.html.twig', ['error' => $error]);
        } else {
            $fiche = $classementSelect->getFiche();

            $classements = $fiche->getClassements();

            foreach ($classements as $classement) {
                if ($classement->getId() === $classementSelect->getId()) {
                    $classement->setPrincipal(true);
                } else {
                    $classement->setPrincipal(false);
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

    /**
     * @Route("/getcategories", name="bottin_admin_ajax_get_categories", methods={"POST"})
     */
    public function ajaxCategories(Request $request): JsonResponse
    {
        $jsonResponse = new JsonResponse();
        $parentId = (int) $request->get('parentId');
        $level = (int) $request->get('level') + 1; // +1 pour div id ajax response

        $result = [];

        if (0 === $parentId) {
            $jsonResponse->setData(['error' => 'Oups pas su obtenir les catégories']);

            return $jsonResponse;
        }

        $categories = $this->categoryRepository->findBy(['parent' => $parentId], ['name' => 'ASC']);

        $html = '';

        if (count($categories) > 0) {
            $html = $this->renderView(
                '@AcMarcheBottin/admin/classement/_ajaxCategories.html.twig',
                ['categories' => $categories, 'level' => $level]
            );
        }

        $result['html'] = $html;
        $result['catId'] = $parentId;
        $result['level'] = $level;

        $jsonResponse->setData($result);

        return $jsonResponse;
    }

    /**
     * @Route("/getcategoriesforexport", name="bottin_admin_ajax_get_categories_for_export", methods={"POST"})
     */
    public function ajaxCategoriesForExport(Request $request): Response
    {
        $jsonResponse = new JsonResponse();
        $parentId = (int) $request->get('parentId');
        $level = (int) $request->get('level');
        ++$level;

        if (!$parentId) {
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

    /**
     * @Route("/fetch/{query}", name="bottin_admin_fetch")
     */
    public function fetchCategorie(?string $query = null): JsonResponse
    {
        $data = [];
        $i = 0;
        foreach ($this->categoryRepository->search($query) as $category) {
            $data[$i]['id'] = $category->getId();
            $data[$i]['name'] = $category->getName();
            $data[$i]['label'] = $category->getName();
            $data[$i]['value'] = $category->getName();
            ++$i;
        }

        return new JsonResponse($data);
    }
}
