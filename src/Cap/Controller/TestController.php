<?php

namespace AcMarche\Bottin\Cap\Controller;

use AcMarche\Bottin\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[Route(path: '/test')]
#[IsGranted('ROLE_BOTTIN_ADMIN')]
class TestController extends AbstractController
{
    private readonly HttpClientInterface $httpClient;

    public function __construct(private readonly CategoryRepository $categoryRepository)
    {
        $httpClient = HttpClient::create(
            [
                'verify_peer' => false,
                'verify_host' => false,
            ]
        );
        $this->httpClient = $httpClient;
    }

    #[Route(path: '/', name: 'bottin_admin_api_test_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('@AcMarcheBottin/admin/test/index.html.twig');
    }

    #[Route(path: '/fiches', name: 'bottin_admin_api_test_fiches', methods: ['GET'])]
    public function fiches(): Response
    {
        $url = $this->generateUrl('bottin_admin_api_fiches_commerces', [], false);
        $request = $this->httpClient->request('GET', $url);
        $fiches = json_decode($request->getContent(), null, 512, \JSON_THROW_ON_ERROR);

        return $this->render('@AcMarcheBottin/admin/test/fiches.html.twig', ['fiches' => $fiches, 'url' => $url]);
    }

    #[Route(path: '/commerces', name: 'bottin_admin_api_test_commerces', methods: ['GET'])]
    public function commerces(): Response
    {
        $url = $this->generateUrl('bottin_admin_api_commerces', [], false);
        $request = $this->httpClient->request('GET', $url);
        $categories = json_decode($request->getContent(), null, 512, \JSON_THROW_ON_ERROR);

        return $this->render(
            '@AcMarcheBottin/admin/test/commerce.html.twig',
            ['categories' => $categories, 'url' => $url]
        );
    }

    #[Route(path: '/fiches/rubrique/{id}', name: 'bottin_admin_api_test_fiche_by_category', methods: ['GET'])]
    public function ficheByCategory($id): Response
    {
        $url = $this->generateUrl('bottin_admin_api_fiche_by_category', ['id' => $id], false);
        $request = $this->httpClient->request('GET', $url);
        $fiches = json_decode($request->getContent(), null, 512, \JSON_THROW_ON_ERROR);
        $category = $this->categoryRepository->find($id);

        return $this->render(
            '@AcMarcheBottin/admin/test/fiches.html.twig',
            ['fiches' => $fiches, 'category' => $category, 'url' => $url]
        );
    }

    #[Route(path: '/fiche/{id}', name: 'bottin_admin_api_test_fiche_id', methods: ['GET'])]
    public function ficheId($id): Response
    {
        $url = $this->generateUrl('bottin_admin_api_fiche_by_id', ['id' => $id], false);
        $request = $this->httpClient->request('GET', $url);
        $fiche = json_decode($request->getContent(), null, 512, \JSON_THROW_ON_ERROR);

        return $this->render('@AcMarcheBottin/admin/test/fiche.html.twig', ['fiche' => $fiche, 'url' => $url]);
    }

    #[Route(path: '/fichebyids', name: 'bottin_admin_api_test_fiche_ids', methods: ['GET'])]
    public function ficheIds(): Response
    {
        $ids = json_encode([393, 522, 55]);
        $fields = ['ids' => $ids];
        $url = $this->generateUrl('bottin_admin_api_fiche_by_ids', [], false);
        try {
            $request = $this->httpClient->request(
                'POST',
                $url,
                [
                    'body' => $fields,
                ]
            );
            $result = json_decode($request->getContent(), null, 512, \JSON_THROW_ON_ERROR);
        } catch (TransportExceptionInterface $transportException) {
            $result = ['error1', $transportException->getMessage()];
        }

        return $this->render(
            '@AcMarcheBottin/admin/test/fiches_ids.html.twig',
            [
                'fiches' => $result,
                'url' => $url,
            ]
        );
    }

    #[Route(path: '/fiche/slug/{slug}', name: 'bottin_admin_api_test_fiche_slug', methods: ['GET'])]
    public function ficheSlug($slug): Response
    {
        $url = $this->generateUrl('bottin_admin_api_fiche_by_slugname', ['slugname' => $slug], false);
        $request = $this->httpClient->request('GET', $url);
        $fiche = json_decode($request->getContent(), null, 512, \JSON_THROW_ON_ERROR);

        return $this->render('@AcMarcheBottin/admin/test/fiche.html.twig', ['fiche' => $fiche, 'url' => $url]);
    }

    #[Route(path: '/updatefiche', name: 'bottin_admin_api_test_update_fiche', methods: ['GET'])]
    public function updatefiche(): Response
    {
        $fields = ['id' => 393, 'fax' => '084 12 34 56', 'gsm' => '0476 12 34 56'];
        $url = $this->generateUrl('bottin_admin_api_update_fiche', [], false);
        $request = $this->httpClient->request(
            'POST',
            $url,
            [
                'body' => $fields,
            ]
        );
        $content = $request->getContent();

        return $this->render('@AcMarcheBottin/admin/test/update_fiche.html.twig', ['result' => $content]);
    }

    #[Route(path: '/bottin/test/search/{keyword}', name: 'bottin_admin_api_test_search', methods: ['GET'])]
    public function testSearch(string $keyword = 'axa'): JsonResponse
    {
        $data = ['keyword' => $keyword];
        $url = $this->generateUrl('bottin_admin_api_search', [], false);
        try {
            $request = $this->httpClient->request(
                'POST',
                $url,
                [
                    'body' => $data,
                ]
            );
        } catch (TransportExceptionInterface $transportException) {
            return $this->json(['error' => $transportException->getMessage()]);
        }

        try {
            $content = $request->getContent();

            return $this->json($content);
        } catch (ClientExceptionInterface|TransportExceptionInterface|ServerExceptionInterface|RedirectionExceptionInterface $e) {
            return $this->json(['error' => $e->getMessage()]);
        }
    }
}
