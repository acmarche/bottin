<?php

namespace AcMarche\Bottin\Controller\Front;

use AcMarche\Bottin\Search\SearchEngineInterface;
use AcMarche\Bottin\Tag\Repository\TagRepository;
use AcMarche\Bottin\Tag\TagUtils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MapController extends AbstractController
{
    public function __construct(
        private readonly TagRepository $tagRepository,
        private readonly TagUtils $tagUtils,
        private readonly SearchEngineInterface $searchEngine,
    ) {
    }

    #[Route(path: '/map', name: 'bottin_map_home')]
    public function index(): Response
    {
        $tag = $this->tagRepository->findOneByName('Circuit-Court');

        try {
            $response = $this->searchEngine->doSearchMap(null, [$tag]);
            //dd($response);
            $hits = $response->getHits();
            $count = $response->count();
            $facetDistribution = $response->getFacetDistribution();
            $icons = $this->tagUtils->getIconsFromFacet($facetDistribution);

        } catch (\Exception $e) {
            $this->addFlash('danger', 'Erreur dans la recherche: '.$e->getMessage());
        }

        return $this->render(
            '@AcMarcheBottin/tailwind/map.html.twig',
            [
                'hits' => $hits,
                'tag' => $tag,
                'icons' => $icons,
                'count' => $count,
                'facetDistribution' => $facetDistribution,
                'selected' => [],
            ]
        );
    }

    #[Route(path: '/map/search', name: 'bottin_map_update')]
    public function upsearch(Request $request): Response
    {
        if ($request->isXmlHttpRequest()) {

            try {
                $response = $this->searchEngine->doSearchMap($args['localite']);
                //dd($response);
                $hits = $response->getHits();
                $count = $response->count();
                $facetDistribution = $response->getFacetDistribution();
                $icons = $this->tagUtils->getIconsFromFacet($facetDistribution);

            } catch (\Exception $e) {
                $this->addFlash('danger', 'Erreur dans la recherche: '.$e->getMessage());
            }
        }

        return $this->render(
            '@AcMarcheBottin/tailwind/map.html.twig',
            [
                'hits' => $hits,
                'icons' => $icons,
                'count' => $count,
                'facetDistribution' => $facetDistribution,
                'selected' => [],
            ]
        );
    }
}
