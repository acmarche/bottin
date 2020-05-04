<?php

namespace AcMarche\Bottin\Controller;

use AcMarche\Bottin\Message\FicheUpdated;
use AcMarche\Bottin\Repository\CategoryRepository;
use AcMarche\Bottin\Repository\ClassementRepository;
use AcMarche\Bottin\Utils\PathUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Ajax controller.
 *
 * @Route("/ajax")
 * @IsGranted("ROLE_BOTTIN_ADMIN")
 */
class AjaxController extends AbstractController
{
    /**
     * @var ClassementRepository
     */
    private $classementRepository;
    /**
     * @var CategoryRepository
     */
    private $categoryRepository;
    /**
     * @var PathUtils
     */
    private $pathUtils;

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
     * @Route("/removeclassment", name="bottin_ajax_remove_classement", methods={"POST"})
     */
    public function removeClassement(Request $request)
    {
        $classementId = (int)$request->get('classementId');
        $classement = $this->classementRepository->find($classementId);

        if (!$classement) {
            $error = 'classement non trouvé';
            $template = $this->renderView('@AcMarcheBottin/ajax/error.html.twig', ['error' => $error]);
        } else {
            $fiche = $classement->getFiche();
            $this->classementRepository->remove($classement);
            $this->classementRepository->flush();

            $this->dispatchMessage(new FicheUpdated($fiche->getId(), null));

            $classements = $this->classementRepository->getByFiche($fiche);
            $classements = $this->pathUtils->setPathForClassements($classements);

            $template = $this->renderView(
                '@AcMarcheBottin/classement/_list.html.twig',
                ['classements' => $classements]
            );
        }

        return new Response($template);
    }

    /**
     * @Route("/setprincipalclassement", name="bottin_ajax_principal_classement", methods={"POST"})
     */
    public function setPrincipal(Request $request)
    {
        $classementId = (int)$request->get('classementId');
        $classementSelect = $this->classementRepository->find($classementId);

        if (!$classementSelect) {
            $error = 'classement non trouvé';
            $template = $this->renderView('@AcMarcheBottin/ajax/error.html.twig', ['error' => $error]);
        } else {
            $fiche = $classementSelect->getFiche();

            $classements = $fiche->getClassements();

            foreach ($classements as $classement) {
                if ($classement->getId() == $classementSelect->getId()) {
                    $classement->setPrincipal(true);
                } else {
                    $classement->setPrincipal(false);
                }
                $this->classementRepository->persist($classement);
            }

            $this->classementRepository->flush();

            $classements = $this->classementRepository->getByFiche($fiche);
            $classements = $this->pathUtils->setPathForClassements($classements);

            $template = $this->renderView(
                '@AcMarcheBottin/classement/_list.html.twig',
                ['classements' => $classements]
            );
        }

        return new Response($template);
    }

    /**
     * @Route("/getcategories", name="bottin_ajax_get_categories", methods={"POST"})
     */
    public function ajaxCategories(Request $request)
    {
        $response = new JsonResponse();
        $parentId = (int)$request->get('parentId');
        $level = (int)$request->get('level') + 1; // +1 pour div id ajax response

        $result = [];

        if (!$parentId) {
            $response->setData(['error' => 'Oups pas su obtenir les catégories']);

            return $response;
        }

        $categories = $this->categoryRepository->findBy(['parent' => $parentId], ['name' => 'ASC']);

        $html = '';

        if (count($categories) > 0) {
            $html = $this->renderView(
                '@AcMarcheBottin/classement/_ajaxCategories.html.twig',
                ['categories' => $categories, 'level' => $level]
            );
        }

        $result['html'] = $html;
        $result['catId'] = $parentId;
        $result['level'] = $level;

        $response->setData($result);

        return $response;
    }

    /**
     * @Route("/fetch/{query}", name="bottin_fetch")
     */
    public function fetchCategorie(?string $query = null)
    {
        $data = [];
        $i = 0;
        foreach ($this->categoryRepository->search($query) as $category) {
            $data[$i]['id'] = $category->getId();
            $data[$i]['name'] = $category->getName();
            $data[$i]['label'] = $category->getName();
            $data[$i]['value'] = $category->getName();
            $i++;
        }

        return new JsonResponse($data);
    }
}
