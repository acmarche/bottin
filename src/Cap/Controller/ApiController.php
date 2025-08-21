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
use AcMarche\Bottin\Search\SearchMeili;
use AcMarche\Bottin\Tag\Repository\TagRepository;
use AcMarche\Bottin\Tag\TagUtils;
use AcMarche\Bottin\Utils\SortUtils;
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
 * https://api.marche.be/admin/updatefiche.
 */
class ApiController extends AbstractController
{
    public function __construct(
        private readonly ApiUtils $apiUtils,
        private readonly DemandeHandler $demandeHandler,
        private readonly CategoryService $categoryService,
        private readonly CategoryRepository $categoryRepository,
        private readonly FicheRepository $ficheRepository,
        private readonly ClassementRepository $classementRepository,
        private readonly TagRepository $tagRepository,
        private readonly SearchMeili $searchMeili,
        private readonly TagUtils $tagUtils,
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
        $data = iterator_to_array($this->getFichesGenerator());

        return $this->json($data);
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

    private function getFichesGenerator(): \Generator
    {
        // Process first category
        foreach ($this->categoryService->getFichesByCategoryId(Cap::idEco) as $fiche) {
            yield $this->apiUtils->prepareFiche($fiche);
        }

        // Process second category
        foreach ($this->categoryService->getFichesByCategoryId(Cap::idPharmacies) as $fiche) {
            yield $this->apiUtils->prepareFiche($fiche);
        }
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
    public function ficheById(int|string $id): JsonResponse
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

    #[Route(path: '/bottin/fichebyname/{name}', methods: ['GET'])]
    public function ficheByName(string $name): JsonResponse
    {
        $data = [];
        $name = base64_decode($name);
        $fiches = $this->ficheRepository->findBy(['societe' => $name]);
        foreach ($fiches as $fiche) {
            $data[] = $this->apiUtils->prepareFiche($fiche);
        }

        return $this->json($data);
    }

    #[Route(path: '/bottin/fichebyemail/{email}', methods: ['GET'])]
    public function ficheByEmail(string $email): JsonResponse
    {
        $data = [];
        $fiches = $this->ficheRepository->findBy(['email' => $email]);
        foreach ($fiches as $fiche) {
            $data[] = $this->apiUtils->prepareFiche($fiche);
        }

        return $this->json($data);
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
    #[Route(path: '/bottin/search', methods: ['POST', 'GET'], name: 'bottin_admin_api_search')]
    public function search(Request $request): JsonResponse
    {
        $keyword = $request->request->get('keyword');
        if (!$keyword) {
            $keyword = $request->query->get('keyword');
        }

        if (!$keyword) {
            return $this->json(['error' => 'Pas de mot clef']);
        }

        try {
            $response = $this->searchMeili->doSearchForCap($keyword);
            $hits = $response->getHits();
            $count = $response->count();
            $result = [
                'hits' => [
                    'total' => [
                        'value' => $count,
                    ],
                    'hits' => [],
                ],
            ];
            $items = [];
            foreach ($hits as $hit) {
                $t = [
                    '_index' => 'bottin',
                    '_id' => $hit['id'],
                    '_source' => $hit,
                ];
                $items[] = $t;
            }
            $result['hits']['hits'] = $items;
        } catch (\Exception $e) {
            $error = 'Erreur dans la recherche: '.$e->getMessage();
            $this->logger->notice('MEILI error '.$e->getMessage());
            $result = ['error' => $error];
        }

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

    #[Route(path: '/bottin/tagbyslug/{slug}', name: 'bottin_admin_api_tagbyslug', methods: ['GET'])]
    public function tagBySlug(string $slug): JsonResponse
    {
        return $this->json($this->tagRepository->findOneByslug($slug));
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
        $tag = $this->tagRepository->find(14);
        $data = [];
        $error = $localite = $coordinates = null;
        $tags = [$tag->name];
        if (Request::METHOD_POST == $request->getMethod()) {
            $post_body = $request->getContent();
            try {
                $this->logger->notice($post_body);
                $args = json_decode($post_body, flags: JSON_THROW_ON_ERROR);
            } catch (\Exception $exception) {
                return $this->json(['error' => 'args not json'], Response::HTTP_BAD_REQUEST);
            }

            if (isset($args->args->localite)) {
                $localite = $args->args->localite;
            }

            if (isset($args->args->tags)) {
                foreach ($args->args->tags as $tag) {
                    $tags[] = $tag;
                }
            }

            if (isset($args->args->coordinates)) {
                $coordinates = $args->args->coordinates;
            }

            try {
                $response = $this->searchMeili->doSearchMap($localite, $tags, $coordinates);
                $hits = $response->getHits();
                $count = $response->count();
                $facetDistribution = $response->getFacetDistribution();
                unset($facetDistribution['type']);
                unset($facetDistribution['capMember']);
                krsort($facetDistribution);
                $icons = $this->tagUtils->getIconsFromFacet($facetDistribution);
            } catch (\Exception $e) {
                $error = 'Erreur dans la recherche: '.$e->getMessage();
                $this->logger->notice('MEILI error '.$e->getMessage());
                $hits = $icons = $facetDistribution = [];
                $count = 0;
            }

            $data['hits'] = SortUtils::sortArrayFiche($hits);
            $data['icons'] = $icons;
            $data['count'] = $count;
            $data['error'] = $error;
            $data['facetDistribution'] = $facetDistribution;
            $filters = [];
            foreach ($facetDistribution as $key => $facets) {
                if (str_starts_with($key, '_')) {
                    continue;
                }
                foreach ($facets as $name => $count) {
                    if ('tags' === $key) {
                        if ($tag = $this->tagRepository->findOneByName($name)) {
                            $filters[$tag->groupe][] = [
                                'name' => $name,
                                'slug' => $tag->getSlug(),
                                'count' => $count,
                                'description' => $tag->description,
                            ];
                        }
                        continue;
                    }
                    $filters[$key][] = ['name' => $name, 'count' => $count, 'slug' => null];
                }
            }

            $data['filters'] = $filters;
        }

        return $this->json($data);
    }

    #[Route(path: '/map/debug')]
    public function mapSearchDebug(Request $request): JsonResponse
    {
        $tag = $this->tagRepository->find(14);
        $data = [];
        $error = $localite = $coordinates = null;
        $tags = [$tag->name];

        try {
            $response = $this->searchMeili->doSearchMap($localite, $tags, $coordinates);
            $hits = $response->getHits();
            $count = $response->count();
            $facetDistribution = $response->getFacetDistribution();
            unset($facetDistribution['type']);
            unset($facetDistribution['capMember']);
            krsort($facetDistribution);
            $icons = $this->tagUtils->getIconsFromFacet($facetDistribution);
        } catch (\Exception $e) {
            $error = 'Erreur dans la recherche: '.$e->getMessage();
            $this->logger->notice('MEILI error '.$e->getMessage());
            $hits = $icons = $facetDistribution = [];
            $count = 0;
        }

        $data['hits'] = SortUtils::sortArrayFiche($hits);
        $data['icons'] = $icons;
        $data['count'] = $count;
        $data['error'] = $error;
        $data['facetDistribution'] = $facetDistribution;

        foreach ($facetDistribution as $key => $facets) {
            if (str_starts_with($key, '_')) {
                continue;
            }
            foreach ($facets as $name => $count) {
                if ('tags' === $key) {
                    if ($tag = $this->tagRepository->findOneByName($name)) {
                        $filters[$tag->groupe][] = [
                            'name' => $name,
                            'slug' => $tag->getSlug(),
                            'count' => $count,
                            'description' => $tag->description,
                        ];
                    }
                    continue;
                }
                $filters[$key][] = ['name' => $name, 'count' => $count, 'slug' => null];
            }
        }

        $data['filters'] = $filters;

        return $this->json($data);
    }
}
