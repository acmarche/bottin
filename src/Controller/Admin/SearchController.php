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
    public function ficher(Request $request, ?string $keyword): Response
    {
        $args = $facetDistribution = $facetStats = [];
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
                $facetStats = $response->getFacetStats();

            } catch (\Exception $e) {
                $this->addFlash('danger', 'Erreur dans la recherche: '.$e->getMessage());
            }
        }

        return $this->render(
            '@AcMarcheBottin/admin/fiche/search.html.twig',
            [
                'search_form' => $form->createView(),
                'isSubmitted' => $form->isSubmitted(),
                'hits' => $hits,
                'count' => $count,
                'facetDistribution' => $facetDistribution,
                'facetStats' => $facetStats,
            ]
        );
    }
}
