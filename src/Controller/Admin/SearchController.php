<?php

namespace AcMarche\Bottin\Controller\Admin;

use AcMarche\Bottin\Form\Search\SearchFicheType;
use AcMarche\Bottin\Search\SearchEngineInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SearchController extends AbstractController
{
    public function __construct(
        private readonly SearchEngineInterface $searchEngine,
    ) {
    }

    #[Route(path: '/searchadvanced', name: 'bottin_admin_fiche_search_advanced', methods: ['GET'])]
    #[Route(path: '/searchadvanced/{keyword}', name: 'bottin_admin_fiche_search', methods: ['GET'])]
    public function fiche(Request $request, ?string $keyword): Response
    {
        $args = $facetDistribution = [];
        $hits = [];
        $count = 0;

        if ($keyword) {
            $args['nom'] = $keyword;
        }

        $form = $this->createForm(SearchFicheType::class, $args, ['method' => 'GET']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $args = $form->getData();

            try {
                $response = $this->searchEngine->doSearchAdvanced($args['nom'], $args['localite']);
                //dd($response);
                $hits = $response->getHits();
                $count = $response->count();
                $facetDistribution = $response->getFacetDistribution();

            } catch (\Exception $e) {
                $this->addFlash('danger', 'Erreur dans la recherche: '.$e->getMessage());
            }
        }

        return $this->render(
            '@AcMarcheBottin/admin/search/advance.html.twig',
            [
                'search_form' => $form->createView(),
                'isSubmitted' => $form->isSubmitted(),
                'hits' => $hits,
                'count' => $count,
                'facetDistribution' => $facetDistribution,
                'selected' => [],
                'keyword' => $keyword,
            ]
        );
    }

    #[Route(path: '/searchadvanced/update', name: 'bottin_admin_fiche_search_update', methods: [
        'POST',
    ])]
    public function searchUp(Request $request, ?string $keyword = ''): ?Response
    {
        if ($request->isXmlHttpRequest()) {
            $localites = $request->request->all('localite');
            $tags = $request->request->all('tags');
            $hits = $selected = [];
            $localite = null;
            if (count($localites) > 0) {
                $localite = $localites[0];
                $selected[] = $localite;
            }
            if (count($tags) > 0) {
                $selected = array_merge($selected, $tags);
            }

            try {
                $response = $this->searchEngine->doSearchAdvanced($keyword, $localite, $tags);
                $hits = $response->getHits();
                $count = $response->count();
                $facetDistribution = $response->getFacetDistribution();

            } catch (\Exception $e) {
                $this->addFlash(
                    'danger',
                    'Erreur dans la recherche: '.$e->getMessage().' line '.$e->getLine().' file '.$e->getFile()
                );
                $hits['error'] = $e->getMessage();
            }

            return $this->render(
                '@AcMarcheBottin/admin/search/_content.html.twig',
                [
                    'hits' => $hits,
                    'count' => $count,
                    'facetDistribution' => $facetDistribution,
                    'selected' => $selected,
                ]
            );

        }

        return null;
    }
}
