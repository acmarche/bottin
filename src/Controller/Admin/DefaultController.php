<?php

namespace AcMarche\Bottin\Controller\Admin;

use AcMarche\Bottin\Search\SearchMeili;
use AcMarche\Bottin\Tag\Repository\TagRepository;
use AcMarche\Bottin\Tag\TagUtils;
use AcMarche\Bottin\Utils\SortUtils;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_BOTTIN_ADMIN')]
#[Route(path: '/admin')]
class DefaultController extends AbstractController
{
    public function __construct(
        private readonly TagRepository $tagRepository,
        private readonly SearchMeili $searchMeili,
        private readonly TagUtils $tagUtils,
        private readonly LoggerInterface $logger
    ) {
    }

    #[Route(path: '/parameters', name: 'bottin_admin_parameter_index')]
    public function index(): Response
    {
        $tag = $this->tagRepository->find(14);
        $data = [];
        $error = $localite = $coordinates = null;
        $tags = [$tag->name];

        try {
            $response = $this->searchMeili->doSearchMap($localite, $tags, $coordinates);
            $hits = $response->getHits();
            $count = $response->count();
            $facetDistribution = $response->getFacetDistribution();
            dump($facetDistribution);
            unset($facetDistribution['type']);
            unset($facetDistribution['CapMember']);
            krsort($facetDistribution);
            $icons = $this->tagUtils->getIconsFromFacet($facetDistribution);
        } catch (\Exception $e) {
            $error = 'Erreur dans la recherche: '.$e->getMessage();
            $this->logger->notice('MEILI error '.$e->getMessage());
            $hits = $icons = $facetDistribution = [];
            $count = 0;
        }

        $data['hits'] = SortUtils::sortArrayFiche($hits);
        $data['icons'] = $icons;
        $data['count'] = $count;
        $data['error'] = $error;
        $data['facetDistribution'] = $facetDistribution;

        foreach ($facetDistribution as $key => $facets) {
            if (str_starts_with($key, '_')) {
                continue;
            }
            foreach ($facets as $name => $count) {
                if ('tags' === $key) {
                    if ($tag = $this->tagRepository->findOneByName($name)) {
                        $filters[$tag->groupe][] = [
                            'name' => $name,
                            'slug' => $tag->getSlug(),
                            'count' => $count,
                            'description' => $tag->description,
                        ];
                    }
                    continue;
                }
                $filters[$key][] = ['name' => $name, 'count' => $count, 'slug' => null];
            }
        }

        dump($filters);
        $data['filters'] = $filters;

        return $this->render(
            '@AcMarcheBottin/admin/parameter/index.html.twig',
            [
            ]
        );
    }
}
