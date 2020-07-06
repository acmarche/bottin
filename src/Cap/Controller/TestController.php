<?php


namespace AcMarche\Bottin\Cap\Controller;

use AcMarche\Bottin\Repository\CategoryRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 *
 * @package AcMarche\Bottin\Controller
 * @Route("/test")
 * @IsGranted("ROLE_BOTTIN_ADMIN")
 */
class TestController extends AbstractController
{
    /**
     * @var HttpClientInterface
     */
    private $httpClient;
    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $httpClient = HttpClient::create(
            [
                'verify_peer' => false,
                'verify_host' => false,
            ]
        );
        $this->httpClient = $httpClient;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @Route("/", name="bottin_api_test_index", methods={"GET"})
     */
    public function index(): Response
    {
        return $this->render('@AcMarcheBottin/test/index.html.twig');
    }

    /**
     * @Route("/fiches", name="bottin_api_test_fiches", methods={"GET"})
     */
    public function fiches(): Response
    {
        $url = $this->generateUrl('bottin_api_fiches_commerces', [], false);
        $request = $this->httpClient->request("GET", $url);
        $fiches = json_decode($request->getContent());

        return $this->render('@AcMarcheBottin/test/fiches.html.twig', ['fiches' => $fiches, 'url' => $url]);
    }

    /**
     * @Route("/commerces", name="bottin_api_test_commerces", methods={"GET"})
     */
    public function commerces(): Response
    {
        $url = $this->generateUrl('bottin_api_commerces', [], false);
        $request = $this->httpClient->request("GET", $url);
        $categories = json_decode($request->getContent());

        return $this->render('@AcMarcheBottin/test/commerce.html.twig', ['categories' => $categories, 'url' => $url]);
    }

    /**
     * @Route("/fiches/rubrique/{id}", name="bottin_api_test_fiche_by_category", methods={"GET"})
     */
    public function ficheByCategory($id): Response
    {
        $url = $this->generateUrl('bottin_api_fiche_by_category', ['id' => $id], false);
        $request = $this->httpClient->request("GET", $url);
        $fiches = json_decode($request->getContent());
        $category = $this->categoryRepository->find($id);

        return $this->render(
            '@AcMarcheBottin/test/fiches.html.twig',
            ['fiches' => $fiches, 'category' => $category, 'url' => $url]
        );
    }

    /**
     * @Route("/fiche/{id}", name="bottin_api_test_fiche_id", methods={"GET"})
     */
    public function ficheId($id): Response
    {
        $url = $this->generateUrl('bottin_api_fiche_by_id', ['id' => $id], false);
        $request = $this->httpClient->request("GET", $url);
        $fiche = json_decode($request->getContent());

        return $this->render('@AcMarcheBottin/test/fiche.html.twig', ['fiche' => $fiche, 'url' => $url]);
    }

    /**
     * @Route("/fichebyids", name="bottin_api_test_fiche_ids", methods={"GET"})
     */
    public function ficheIds(): Response
    {
        $ids = json_encode([393, 522, 55]);
        $fields = ['ids' => $ids];

        $url = $this->generateUrl('bottin_api_fiche_by_ids', [], false);
        try {
            $request = $this->httpClient->request(
                "POST",
                $url,
                [
                    'body' => $fields,
                ]
            );
            $result = json_decode($request->getContent());
        } catch (TransportExceptionInterface $e) {
            $result = ['error1', $e->getMessage()];
        }

        return $this->render(
            '@AcMarcheBottin/test/fiches_ids.html.twig',
            [
                'fiches' => $result,
                'url' => $url,
            ]
        );
    }

    /**
     * @Route("/fiche/slug/{slug}", name="bottin_api_test_fiche_slug", methods={"GET"})
     */
    public function ficheSlug($slug): Response
    {
        $url = $this->generateUrl('bottin_api_fiche_by_slugname', ['slugname' => $slug], false);
        $request = $this->httpClient->request("GET", $url);
        $fiche = json_decode($request->getContent());

        return $this->render('@AcMarcheBottin/test/fiche.html.twig', ['fiche' => $fiche, 'url' => $url]);
    }

    /**
     *
     * @Route("/updatefiche", name="bottin_api_test_update_fiche", methods={"GET"})
     */
    public function updatefiche(): Response
    {
        $fields = array('id' => 393, 'fax' => '084 12 34 56', 'gsm' => '0476 12 34 56');

        $url = $this->generateUrl('bottin_api_update_fiche', [], false);
        $request = $this->httpClient->request(
            "POST",
            $url,
            [
                'body' => $fields,
            ]
        );

        $result = json_decode($request->getContent());

        return $this->render('@AcMarcheBottin/test/update_fiche.html.twig', ['result' => $result]);
    }

    /**
     * @Route("/bottin/test/search/{keyword}", name="bottin_api_test_search", methods={"GET"})
     *
     */
    public function testSearch(string $keyword = 'axa'): Response
    {
        $data = ['keyword' => $keyword];

        $url = $this->generateUrl('bottin_api_search', [], false);
        $request = $this->httpClient->request(
            "POST",
            $url,
            [
                'body' => $data,
            ]
        );

        return new Response($request->getContent());
    }

}
