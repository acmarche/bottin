<?php


namespace AcMarche\Bottin\Cap\Controller;

use AcMarche\Bottin\Cap\ApiUtils;
use AcMarche\Bottin\Cap\Cap;
use AcMarche\Bottin\Elastic\ElasticServer;
use AcMarche\Bottin\Entity\Category;
use AcMarche\Bottin\Entity\Fiche;
use AcMarche\Bottin\Repository\CategoryRepository;
use AcMarche\Bottin\Repository\FicheRepository;
use AcMarche\Bottin\Service\CategoryService;
use AcMarche\Bottin\Service\DemandeHandler;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ApiController
 * @package AcMarche\Bottin\Controller
 *
 */
class ApiController extends AbstractController
{
    /**
     * @var CategoryService
     */
    private $categoryService;
    /**
     * @var FicheRepository
     */
    private $ficheRepository;
    /**
     * @var CategoryRepository
     */
    private $categoryRepository;
    /**
     * @var ApiUtils
     */
    private $apiUtils;
    /**
     * @var DemandeHandler
     */
    private $demandeHandler;
    /**
     * @var ElasticServer
     */
    private $elasticServer;

    public function __construct(
        ApiUtils $apiUtils,
        DemandeHandler $demandeHandler,
        CategoryService $categoryService,
        CategoryRepository $categoryRepository,
        FicheRepository $ficheRepository,
        ElasticServer $elasticServer
    ) {
        $this->categoryService = $categoryService;
        $this->ficheRepository = $ficheRepository;
        $this->categoryRepository = $categoryRepository;
        $this->apiUtils = $apiUtils;
        $this->demandeHandler = $demandeHandler;
        $this->elasticServer = $elasticServer;
    }

    /**
     * Fiches par categorie
     *
     * @Route("/bottin/fiches/category/{id}", name="bottin_api_fiche_by_category", methods={"GET"}, format="json")
     */
    public function fichesByCategory(Category $category)
    {
        $data = [];
        $fiches = $this->categoryService->getFichesByCategoryAndHerChildren($category);

        foreach ($fiches as $fiche) {
            $data[] = $this->apiUtils->prepareFiche($fiche);
        }

        return $this->json($data);
    }

    /**
     * Toutes les rubriques de commerces
     *
     * @Route("/bottin/commerces", name="bottin_api_commerces", methods={"GET"}, format="json")
     */
    public function commerce(): JsonResponse
    {
        $data = $this->apiUtils->prepareCategories($this->categoryRepository->getRubriquesShopping());

        return $this->json($data);
    }

    /**
     * Toutes les fiches des commerces
     *
     * @Route("/bottin/fiches", name="bottin_api_fiches_commerces", methods={"GET"}, format="json")
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
     * Le detail de la fiche {id}
     * @Route("/bottin/fichebyid/{id}", name="bottin_api_fiche_by_id", methods={"GET"}, format="json")
     * @param Fiche $fiche
     * @return JsonResponse
     */
    public function ficheById(Fiche $fiche): JsonResponse
    {
        return $this->json($this->apiUtils->prepareFiche($fiche));
    }

    /**
     * Le detail de la fiche {slugname}
     * @Route("/bottin/fichebyslugname/{slugname}", name="bottin_api_fiche_by_slugname", methods={"GET"}, format="json")
     * @ParamConverter("fiche", options={"mapping": {"slugname": "slug"}})
     * @param Fiche $fiche
     * @return JsonResponse
     */
    public function ficheBySlug(Fiche $fiche): JsonResponse
    {
        return $this->json($this->apiUtils->prepareFiche($fiche));
    }

    /**
     *
     * @Route("/updatefiche", name="bottin_api_update_fiche", methods={"POST"}, format="json")
     */
    public function updatefiche(Request $request): JsonResponse
    {
        $data = $request->request->all();
        $result = $this->demandeHandler->handle($data);

        return $this->json($result);
    }

    /**
     * $urlCurl = "https://api.marche.be/search/bottin/fiches/_search";
     * @Route("/bottin/search", name="bottin_api_search", methods={"POST"})
     */
    public function search(Request $request): JsonResponse
    {
        $keyword = $request->request->get('keyword');
        $result = $this->elasticServer->doSearchForCap($keyword);

        return $this->json($result);
    }

}
