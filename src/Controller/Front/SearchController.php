<?php

namespace AcMarche\Bottin\Controller\Front;

use AcMarche\Bottin\Form\Search\SearchSimpleType;
use AcMarche\Bottin\Search\SearchEngineInterface;
use Elasticsearch\Common\Exceptions\BadRequest400Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
    private SearchEngineInterface $searchEngine;

    public function __construct(
        SearchEngineInterface $searchEngine
    ) {
        $this->searchEngine = $searchEngine;
    }

    /**
     * @Route("/search", name="bottin_front_search")
     */
    public function search(Request $request): Response
    {
        $hits = [];
        $keyword = null;
        $count = 0;
        $form = $this->createForm(SearchSimpleType::class, [], ['method' => 'GET']);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $args = $form->getData();
            $keyword = $args['nom'];

            try {
                $response = $this->searchEngine->doSearch($keyword);
                $hits = $response->getResults();
                $count = $response->count();
            } catch (\Exception $e) {
                $this->addFlash('danger', 'Erreur dans la recherche: '.$e->getMessage());
            }
        }

        return $this->render(
            '@AcMarcheBottin/front/search/index.html.twig',
            [
                'form' => $form->createView(),
                'hits' => $hits,
                'keyword' => $keyword,
                'count' => $count,
            ]
        );
    }

    /**
     * @Route("/search/form", name="bottin_front_search_form")
     */
    public function searchForm(): Response
    {
        $form = $this->createForm(
            SearchSimpleType::class,
            [],
            [
                'method' => 'GET',
                'action' => $this->generateUrl('bottin_front_search'),
            ]
        );

        return $this->render(
            '@AcMarcheBottin/front/search/_form.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/search/ajax", name="bottin_front_search_ajax")
     */
    public function searchAjax(Request $request): Response
    {
        $hits = [];
        $q = $request->query->get('q');

        try {
            $response = $this->searchEngine->doSearch($q);
            $hits = $response['hits'];
        } catch (BadRequest400Exception $e) {
            $this->addFlash('danger', 'Erreur dans la recherche: '.$e->getMessage());
        }

        return $this->render(
            '@AcMarcheBottin/front/search/_ajax.html.twig',
            [
                'hits' => $hits,
            ]
        );
    }
}
