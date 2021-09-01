<?php

namespace AcMarche\Bottin\Controller\Admin;

use AcMarche\Bottin\Elasticsearch\AggregationUtils;
use AcMarche\Bottin\Elasticsearch\SuggestUtils;
use AcMarche\Bottin\Form\Search\SearchFicheType;
use AcMarche\Bottin\Search\SearchEngineInterface;
use Elasticsearch\Common\Exceptions\BadRequest400Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
    private AggregationUtils $aggregationUtils;
    private SuggestUtils $suggestUtils;
    private SearchEngineInterface $searchEngine;

    public function __construct(
        SearchEngineInterface $searchEngine,
        AggregationUtils $aggregationUtils,
        SuggestUtils $suggestUtils
    ) {
        $this->aggregationUtils = $aggregationUtils;
        $this->suggestUtils = $suggestUtils;
        $this->searchEngine = $searchEngine;
    }

    /**
     * @Route("/searchadvanced", name="bottin_admin_fiche_search_advanced", methods={"GET"})
     * @Route("/searchadvanced/{keyword}", name="bottin_admin_fiche_search", methods={"GET"})
     */
    public function ficher(Request $request, ?string $keyword): Response
    {
        $session = $request->getSession();
        $args = $hits = $suggest = $aggregations = $pmr = $centreville = $midi = [];
        $count = 0;

        if ($session->has('fiche_search')) {
            $args = json_decode($session->get('fiche_search'), true);
        }

        if ($keyword) {
            $args['nom'] = $keyword;
        }
        $form = $this->createForm(SearchFicheType::class, $args, ['method' => 'GET']);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $args = $form->getData();
            $session->set('fiche_search', json_encode($args));

            try {
                $response = $this->searchEngine->doSearchAdvanced($args['nom'], $args['localite']);
                dump($response);
                $hits = $response->getResults();
                $count = $response->count();
                $aggregations = $this->aggregationUtils->getAggregations($response, 'localites');
                $pmr = $this->aggregationUtils->countPmr($response);
                $midi = $this->aggregationUtils->countMidi($response);
                $centreville = $this->aggregationUtils->countCentreVille($response);
                $suggest = $this->suggestUtils->getOptions($response);
            } catch (BadRequest400Exception $e) {
                $this->addFlash('danger', 'Erreur dans la recherche: '.$e->getMessage());
            }
        }

        return $this->render(
            '@AcMarcheBottin/admin/fiche/search.html.twig',
            [
                'search_form' => $form->createView(),
                'hits' => $hits,
                'count' => $count,
                'localites' => $aggregations,
                'pmr' => $pmr,
                'midi' => $midi,
                'centre_ville' => $centreville,
                'suggests' => $suggest,
            ]
        );
    }
}
