<?php

namespace AcMarche\Bottin\Cap\Controller;

use AcMarche\Bottin\Cap\ApiUtils;
use AcMarche\Bottin\Cap\Cap;
use AcMarche\Bottin\Entity\Category;
use AcMarche\Bottin\Entity\Fiche;
use AcMarche\Bottin\Repository\CategoryRepository;
use AcMarche\Bottin\Repository\ClassementRepository;
use AcMarche\Bottin\Repository\FicheRepository;
use AcMarche\Bottin\Search\SearchEngineInterface;
use AcMarche\Bottin\Service\CategoryService;
use AcMarche\Bottin\Service\DemandeHandler;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ApiController.
 */
class ApiController extends AbstractController
{
    private CategoryService $categoryService;
    private FicheRepository $ficheRepository;
    private CategoryRepository $categoryRepository;
    private ApiUtils $apiUtils;
    private DemandeHandler $demandeHandler;
    private SearchEngineInterface $searchEngine;
    private ClassementRepository $classementRepository;
    private LoggerInterface $logger;

    public function __construct(
        ApiUtils $apiUtils,
        DemandeHandler $demandeHandler,
        CategoryService $categoryService,
        CategoryRepository $categoryRepository,
        FicheRepository $ficheRepository,
        SearchEngineInterface $searchEngine,
        ClassementRepository $classementRepository,
        LoggerInterface $logger
    ) {
        $this->categoryService = $categoryService;
        $this->ficheRepository = $ficheRepository;
        $this->categoryRepository = $categoryRepository;
        $this->apiUtils = $apiUtils;
        $this->demandeHandler = $demandeHandler;
        $this->searchEngine = $searchEngine;
        $this->classementRepository = $classementRepository;
        $this->logger = $logger;
    }

    /**
     * Fiches par categorie.
     *
     * @Route("/bottin/fiches/category/{id}", name="bottin_admin_api_fiche_by_category", methods={"GET"})
     */
    public function fichesByCategory(Category $category): JsonResponse
    {
        $data = [];
        $fiches = $this->categoryService->getFichesByCategoryAndHerChildren($category);

        foreach ($fiches as $fiche) {
            $data[] = $this->apiUtils->prepareFiche($fiche);
        }

        return $this->json($data);
    }

    /**
     * Toutes les rubriques de commerces.
     *
     * @Route("/bottin/commerces", name="bottin_admin_api_commerces", methods={"GET"})
     */
    public function commerce(): JsonResponse
    {
        $data = $this->apiUtils->prepareCategories($this->categoryRepository->getRubriquesShopping());

        return $this->json($data);
    }

    /**
     * Toutes les fiches des commerces.
     *
     * @Route("/bottin/fiches", name="bottin_admin_api_fiches_commerces", methods={"GET"})
     */
    public function fiches(): JsonResponse
    {
        $fiches = array_merge(
            $this->categoryService->getFichesByCategoryId(Cap::idEco),
            $this->categoryService->getFichesByCategoryId(Cap::idPharmacies)
        );

        $data = [];
        foreach ($fiches as $fiche) {
            $data[] = $this->apiUtils->prepareFiche($fiche);
        }

        /*     $parsedQuestionText = $cache->get('markdown_'.md5($questionText), function() use ($questionText, $markdownParser) {
                 return $markdownParser->transformMarkdown($questionText);
             });*/

        return $this->json($data);
    }

    /**
     * Toutes les fiches pour android.
     *
     * @Route("/bottin/fichesandroid", name="bottin_admin_api_fiches_all", methods={"GET"})
     */
    public function fichesAndroid(): JsonResponse
    {
        $fiches = $this->ficheRepository->findAll();

        $data = [];
        foreach ($fiches as $fiche) {
            $data[] = $this->apiUtils->prepareFicheAndroid($fiche);
        }

        return $this->json($data);
    }

    /**
     * Le detail de la fiche {id}.
     *
     * @Route("/bottin/fichebyid/{id}", name="bottin_admin_api_fiche_by_id", methods={"GET"})
     *
     * @param Fiche $fiche
     */
    public function ficheById(int $id): JsonResponse
    {
        $fiche = $this->ficheRepository->find($id);
        if (null === $fiche) {
            return $this->json(['error' => 'Fiche not found']);
        }

        return $this->json($this->apiUtils->prepareFiche($fiche));
    }

    /**
     * Le detail de la fiche {id}.
     *
     * @Route("/bottin/fichebyids", name="bottin_admin_api_fiche_by_ids", methods={"POST"})
     *
     * @param Fiche $fiche
     */
    public function ficheByIds(Request $request): JsonResponse
    {
        $ids = json_decode($request->request->get('ids'), true);

        $fiches = $this->ficheRepository->findByIds($ids);
        $data = [];
        foreach ($fiches as $fiche) {
            $data[] = $this->apiUtils->prepareFiche($fiche);
        }

        return $this->json($data);
    }

    /**
     * Le detail de la fiche {slugname}.
     *
     * @Route("/bottin/fichebyslugname/{slugname}", name="bottin_admin_api_fiche_by_slugname", methods={"GET"})
     * @ParamConverter("fiche", options={"mapping": {"slugname": "slug"}})
     *
     * @param Fiche $fiche
     */
    public function ficheBySlug(string $slugname): JsonResponse
    {
        $fiche = $this->ficheRepository->findOneBy(['slug' => $slugname]);
        if (null === $fiche) {
            return $this->json(['error' => 'Fiche not found']);
        }

        return $this->json($this->apiUtils->prepareFiche($fiche));
    }

    /**
     * @Route("/updatefiche", name="bottin_admin_api_update_fiche", methods={"POST"})
     */
    public function updatefiche(Request $request): JsonResponse
    {
        $data = $request->request->all();
        $result = $this->demandeHandler->handle($data);

        $this->logger->info('api update fiche result' . json_encode($result));

        return $this->json($result);
    }

    /**
     * $urlCurl = "https://api.marche.be/search/bottin/fiches/_search";.
     *
     * @Route("/bottin/search", name="bottin_admin_api_search", methods={"POST"})
     */
    public function search(Request $request): JsonResponse
    {
        $keyword = $request->request->get('keyword');
        if (!$keyword) {
            return $this->json(['error' => 'Pas de mot clef']);
        }
        $result = $this->searchEngine->doSearchForCap($keyword);

        return $this->json($result);
    }

    /**
     * Toutes les classements pour android.
     *
     * @Route("/bottin/classements", name="bottin_admin_api_classements", methods={"GET"})
     */
    public function classements(): JsonResponse
    {
        $classements = $this->classementRepository->findAll();
        $data = [];
        foreach ($classements as $classement) {
            $data[] = $this->apiUtils->prepareClassement($classement);
        }

        return $this->json($data);
    }

    /**
     * Toutes les categories pour android.
     *
     * @Route("/bottin/categories", name="bottin_admin_api_categories", methods={"GET"})
     */
    public function categories(): JsonResponse
    {
        $categories = $this->categoryRepository->findAll();

        return $this->json($this->apiUtils->prepareCategoriesForAndroid($categories));
    }

    /**
     * Toutes les categories sous forme d'arbre.
     *
     * Route("/bottin/categoriestree",  methods={"GET"}, format="json")
     */
    public function categoriesTree(): JsonResponse
    {
        $roots = $this->categoryRepository->getRootNodes();
        $data = [];
        foreach ($roots as $rootNode) {
            $data[] = $this->categoryRepository->getTree($rootNode->getRealMaterializedPath());
        }

        $categories = [];
        foreach ($data as $root) {
            $rootclean = $this->apiUtils->serializeCategoryForAndroid($root);
            $levels1 = [];
            foreach ($root->getChildNodes() as $level1) {
                $levels1[] = $this->apiUtils->serializeCategoryForAndroid($level1);
                $levels2 = [];
                foreach ($level1->getChildNodes() as $level2) {
                    $levels2[] = $this->apiUtils->serializeCategoryForAndroid($level2);
                }
                $levels1['children'] = $levels2;
            }
            $rootclean['children'] = $levels1;
            $categories[] = $rootclean;
            break; //todo not finished
        }

        return $this->json($categories);
    }
}
