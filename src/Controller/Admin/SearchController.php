<?php

namespace AcMarche\Bottin\Controller\Admin;

use AcMarche\Bottin\Form\Search\SearchFicheType;
use AcMarche\Bottin\Search\SearchEngineInterface;
use AcMarche\Bottin\Tag\Repository\TagRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SearchController extends AbstractController
{
    public function __construct(
        private readonly SearchEngineInterface $searchEngine,
        private readonly TagRepository $tagRepository
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
            ]
        );
    }

    #[Route(path: '/searchadvanced/update', name: 'bottin_admin_fiche_search_update', methods: ['GET', 'POST'])]
    public function searchUp(Request $request, ?string $keyword): Response
    {
        $keyword = 'boulanger';
        $localites = $request->request->all('localite');
        $tags = $request->request->all('tags');
        $hits = [];
        $localite = null;
        if (count($localites) > 0) {
            $localite = $localites[0];
        }
        try {
            $response = $this->searchEngine->doSearchAdvanced($keyword, $localite, $tags);
            //dd($response);
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
            '@AcMarcheBottin/admin/search/_result.html.twig',
            [
                'hits' => $hits,
                'count' => $count,
            ]
        );

    }
}
