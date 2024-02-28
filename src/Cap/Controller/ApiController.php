<?php

namespace AcMarche\Bottin\Cap\Controller;

use AcMarche\Bottin\Cap\ApiUtils;
use AcMarche\Bottin\Cap\Cap;
use AcMarche\Bottin\Category\Repository\CategoryService;
use AcMarche\Bottin\Demande\Handler\DemandeHandler;
use AcMarche\Bottin\Entity\Category;
use AcMarche\Bottin\Entity\Fiche;
use AcMarche\Bottin\Repository\CategoryRepository;
use AcMarche\Bottin\Repository\ClassementRepository;
use AcMarche\Bottin\Repository\FicheRepository;
use AcMarche\Bottin\Search\SearchElastic;
use AcMarche\Bottin\Search\SearchEngineInterface;
use AcMarche\Bottin\Tag\Repository\TagRepository;
use AcMarche\Bottin\Tag\TagUtils;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * https://api.marche.be/bottin/fichebyids
 * https://api.marche.be/bottin/fichebyslugname/
 * https://api.marche.be/bottin/fiche/1234
 * https://api.marche.be/bottin/fiches
 * https://api.marche.be/bottin/fiches/rubrique/1234
 * https://api.marche.be/search/bottin/fiches/_search
 * https://api.marche.be/bottin/commerces
 * https://api.marche.be/admin/updatefiche
 */
class ApiController extends AbstractController
{
    public function __construct(
        private readonly ApiUtils $apiUtils,
        private readonly DemandeHandler $demandeHandler,
        private readonly CategoryService $categoryService,
        private readonly CategoryRepository $categoryRepository,
        private readonly FicheRepository $ficheRepository,
        private readonly SearchElastic $searchElastic,
        private readonly ClassementRepository $classementRepository,
        private readonly TagRepository $tagRepository,
        private readonly TagUtils $tagUtils,
        private readonly SearchEngineInterface $searchEngine,
        private readonly LoggerInterface $logger
    ) {
    }


    #[Route(path: '/bottin/cap/search/{id}/{noon}/{sunday}', methods: ['GET'])]
    public function searchCap(Category $category, bool $noon = false, bool $sunday = false): JsonResponse
    {
        $data = [];
        $fiches = $this->categoryService->getFichesByCategoryAndHerChildren($category);

        foreach ($fiches as $fiche) {
            $data[] = $this->apiUtils->prepareFiche($fiche);
        }

        return $this->json($data);
    }

    /**
     * Fiches par categorie.
     */
    #[Route(path: '/bottin/fiches/category/{id}', name: 'bottin_admin_api_fiche_by_category', methods: ['GET'])]
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
     * Fiches par categorie.
     */
    #[Route(path: '/bottin/fiches/category-by-slug/{slug}', methods: ['GET'])]
    public function fichesByCategorySlug(Category $category): JsonResponse
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
     */
    #[Route(path: '/bottin/commerces', name: 'bottin_admin_api_commerces', methods: ['GET'])]
    public function commerce(): JsonResponse
    {
        $data = $this->apiUtils->prepareCategories($this->categoryRepository->getRubriquesShopping());

        return $this->json($data);
    }

    /**
     * Toutes les fiches des commerces.
     */
    #[Route(path: '/bottin/fiches', name: 'bottin_admin_api_fiches_commerces', methods: ['GET'])]
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
     */
    #[Route(path: '/bottin/fichesandroid', name: 'bottin_admin_api_fiches_all', methods: ['GET'])]
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
     */
    #[Route(path: '/bottin/fichebyid/{id}', name: 'bottin_admin_api_fiche_by_id', methods: ['GET'])]
    public function ficheById(int $id): JsonResponse
    {
        $fiche = $this->ficheRepository->find($id);
        if (!$fiche instanceof Fiche) {
            return $this->json(['error' => 'Fiche not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        return $this->json($this->apiUtils->prepareFiche($fiche));
    }

    /**
     * Le detail de la fiche {id}.
     *
     * @throws \JsonException
     */
    #[Route(path: '/bottin/fichebyids', name: 'bottin_admin_api_fiche_by_ids', methods: ['POST'])]
    public function ficheByIds(Request $request): JsonResponse
    {
        $ids = json_decode($request->request->get('ids'), true, 512, \JSON_THROW_ON_ERROR);
        $fiches = $this->ficheRepository->findByIds($ids);
        $data = [];
        foreach ($fiches as $fiche) {
            $data[] = $this->apiUtils->prepareFiche($fiche);
        }

        return $this->json($data);
    }

    /**
     * Le detail de la fiche {slugname}.
     */
    #[Route(path: '/bottin/fichebyslugname/{slugname}', name: 'bottin_admin_api_fiche_by_slugname', methods: ['GET'])]
    public function ficheBySlug(string $slugname): JsonResponse
    {
        $fiche = $this->ficheRepository->findOneBy(['slug' => $slugname]);
        if (!$fiche instanceof Fiche) {
            return $this->json(['error' => 'Fiche not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        return $this->json($this->apiUtils->prepareFiche($fiche));
    }

    #[Route(path: '/updatefiche', name: 'bottin_admin_api_update_fiche', methods: ['POST'])]
    public function updatefiche(Request $request): JsonResponse
    {
        try {
            $data = $request->request->all();
            $result = $this->demandeHandler->handle($data);
            $this->logger->info('api update fiche result'.json_encode($result, \JSON_THROW_ON_ERROR));

            return $this->json($result);
        } catch (\Exception $exception) {
            return $this->json(['error' => $exception->getMessage()]);
        }
    }

    /**
     * $urlCurl = "https://api.marche.be/search/bottin/fiches/_search";.
     */
    #[Route(path: '/bottin/search', name: 'bottin_admin_api_search', methods: ['POST'])]
    public function search(Request $request): JsonResponse
    {
        $keyword = $request->request->get('keyword');
        if (!$keyword) {
            return $this->json(['error' => 'Pas de mot clef']);
        }

        $result = $this->searchElastic->doSearchForCap($keyword);

        return $this->json($result);
    }

    /**
     * Tous les classements pour android.
     */
    #[Route(path: '/bottin/classements', name: 'bottin_admin_api_classements', methods: ['GET'])]
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
     */
    #[Route(path: '/bottin/categories', name: 'bottin_admin_api_categories', methods: ['GET'])]
    public function categories(): JsonResponse
    {
        $categories = $this->categoryRepository->findAll();

        return $this->json($this->apiUtils->prepareCategoriesForAndroid($categories));
    }

    #[Route(path: '/bottin/category/{id}', methods: ['GET'])]
    public function category(int $id): JsonResponse
    {
        if ($category = $this->categoryRepository->find($id)) {

            $children = $this->categoryRepository->getDirectChilds($id);
            $category->enfants = $children;

            $data = $this->apiUtils->serializeCategoryForAndroid($category);
            $enfantsSerialized = [];
            foreach ($category->enfants as $enfant) {
                $dataEnfant = $this->apiUtils->categorySerializer->serializeCategory2($enfant);
                $enfantsSerialized[] = $dataEnfant;
            }
            $data['enfants'] = $enfantsSerialized;

            return $this->json($data);
        }

        return $this->json(null);
    }

    #[Route(path: '/bottin/category-by-slug/{slug}', methods: ['GET'])]
    public function categoryBySlug(string $slug): JsonResponse
    {
        if ($category = $this->categoryRepository->findOneBySlug($slug)) {

            $children = $this->categoryRepository->getDirectChilds($category->getId());
            $category->enfants = $children;

            $data = $this->apiUtils->serializeCategoryForAndroid($category);
            $enfantsSerialized = [];
            foreach ($category->enfants as $enfant) {
                $dataEnfant = $this->apiUtils->categorySerializer->serializeCategory2($enfant);
                $enfantsSerialized[] = $dataEnfant;
            }
            $data['enfants'] = $enfantsSerialized;

            return $this->json($data);
        }

        return $this->json(null);
    }

    #[Route(path: '/bottin/categories/parent/{id}', name: 'bottin_admin_api_categories_by_parent', methods: ['GET'])]
    public function categoriesByParent(int $id): JsonResponse
    {
        if ($id > 0) {
            $categories = $this->categoryRepository->getDirectChilds($id);

            return $this->json($this->apiUtils->prepareCategoriesForAndroid($categories));
        }

        return $this->json([]);
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
            break; // todo not finished
        }

        return $this->json($categories);
    }

    #[Route(path: '/map/update')]
    public function mapSearch(Request $request): JsonResponse
    {
        $tag = $this->tagRepository->findOneByName('Circuit-Court');
        $data = [];
        $error = $localite = null;
        $tags = [$tag->name];
        if ($request->getMethod() == Request::METHOD_POST) {
            $post_body = $request->getContent();
            try {
                $this->logger->error($post_body);
                $args = json_decode($post_body, flags: JSON_THROW_ON_ERROR);
            } catch (\Exception $exception) {
                return $this->json(['error' => 'args not json'], Response::HTTP_BAD_REQUEST);
            }

            if ($args->args->localite) {
                $localite = $args->args->localite;
            }

            if ($args->args->tags) {
                foreach ($args->args->tags as $tag) {
                    $tags[] = $tag;
                }
            }

            //$this->logger->warning('MEILI tags'.json_encode($tags));
            try {
                $response = $this->searchEngine->doSearchMap($localite, $tags);
                //dd($response);
                $hits = $response->getHits();
                $count = $response->count();
                $facetDistribution = $response->getFacetDistribution();
                unset($facetDistribution['type']);
                $icons = $this->tagUtils->getIconsFromFacet($facetDistribution);
            } catch (\Exception $e) {
                $error = 'Erreur dans la recherche: '.$e->getMessage();
                $hits = $icons = $facetDistribution = [];
                $count = 0;
            }

            $this->logger->warning('MEILI count '.$count);
            $data['hits'] = $hits;
            $data['icons'] = $icons;
            $data['count'] = $count;
            $data['error'] = $error;
            $data['facetDistribution'] = $facetDistribution;
        }

        return $this->json($data);
    }
}
