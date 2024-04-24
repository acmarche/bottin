<?php

namespace AcMarche\Bottin\Controller\Front;

use AcMarche\Bottin\Form\Search\SearchSimpleType;
use AcMarche\Bottin\Search\SearchMeili;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SearchController extends AbstractController
{
    public function __construct(private readonly SearchMeili $searchEngine)
    {
    }

    #[Route(path: '/search', name: 'bottin_front_search')]
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
                $hits = $response->getHits();
                $count = $response->count();
            } catch (\Exception $e) {
                $this->addFlash('danger', 'Erreur dans la recherche: '.$e->getMessage());
            }
        }

        return $this->render(
            '@AcMarcheBottin/front/search/index.html.twig',
            [
                'form' => $form->createView(),
                'isSubmitted' => $form->isSubmitted(),
                'hits' => $hits,
                'keyword' => $keyword,
                'count' => $count,
            ]
        );
    }

    #[Route(path: '/search/ajax2', name: 'bottin_front_search_ajax')]
    public function searchAjax2(Request $request): Response
    {
        $hits = [];
        $q = $request->query->get('q');
        try {
            $response = $this->searchEngine->doSearch($q);
            $hits = $response->getHits();
        } catch (\Exception $badRequest400Exception) {
            $this->addFlash('danger', 'Erreur dans la recherche: '.$badRequest400Exception->getMessage());
        }

        return $this->render(
            '@AcMarcheBottin/front/search/_list.html.twig',
            [
                'hits' => $hits,
            ]
        );
    }

    #[Route(path: '/search/form', name: 'bottin_front_search_form')]
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
}
