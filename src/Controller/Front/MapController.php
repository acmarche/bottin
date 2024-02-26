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
            unset($facetDistribution['type']);
            $icons = $this->tagUtils->getIconsFromFacet($facetDistribution);
        } catch (\Exception $e) {
            $this->addFlash('danger', 'Erreur dans la recherche: '.$e->getMessage());
            $hits = $icons = $facetDistribution = [];
            $count = 0;
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
        $hits = $selected = $icons = $facetDistribution = [];
        $count = 0;
        if ($request->getMethod() == 'POST') {
            $tag = $this->tagRepository->findOneByName('Circuit-Court');
            $tags = [$tag->name];
            $localites = $request->request->all('localite');
            $tagsSelected = $request->request->all('tags');
            $localite = null;
            if (count($localites) > 0) {
                $localite = $localites[0];
                $selected[] = $localite;
            }
            if (count($tagsSelected) > 0) {
                //$tags = [$tag];
                $selected = array_merge($selected, $tagsSelected);
            }
            try {
                $response = $this->searchEngine->doSearchMap($localite, $tags);
                //dd($response);
                $hits = $response->getHits();
                $count = $response->count();

                $facetDistribution = $response->getFacetDistribution();
                unset($facetDistribution['type']);
                $icons = $this->tagUtils->getIconsFromFacet($facetDistribution);

            } catch (\Exception $e) {
                $this->addFlash('danger', 'Erreur dans la recherche: '.$e->getMessage());
            }
        }

        return $this->render(
            '@AcMarcheBottin/tailwind/_list.html.twig',
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
