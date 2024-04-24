<?php

namespace AcMarche\Bottin\Controller\Front;

use AcMarche\Bottin\Cap\CapService;
use AcMarche\Bottin\Search\SearchMeili;
use AcMarche\Bottin\Tag\Repository\TagRepository;
use AcMarche\Bottin\Utils\PdfDownloaderTrait;
use AcMarche\Bottin\Utils\SortUtils;
use Psr\Http\Client\ClientInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ExportController extends AbstractController
{
    use PdfDownloaderTrait;

    public function __construct(
        #[Autowire('%kernel.project_dir%')]
        private string $project_dir,
        private readonly TagRepository $tagRepository,
        private readonly SearchMeili $meilisearch,
        private readonly ClientInterface $httpClient,
    ) {
    }

    #[Route(path: '/export/circuit-court')]
    public function index(): Response
    {
        $tag = $this->tagRepository->find(14);
        $localite = $coordinates = null;
        $tags = [$tag->name];

        try {
            $response = $this->meilisearch->doSearchMap($localite, $tags, $coordinates);
            //dd($response);
            $hits = $response->getHits();

        } catch (\Exception $e) {
            $hits = [];
        }

        $hits = SortUtils::sortArrayFiche($hits);
        $hits = array_map(function ($fiche) {
            $fiche['url'] = CapService::generateUrlCapFromArray($fiche);

            return $fiche;
        }, $hits);

        $css = '';
        $html = $this->renderView(
            '@AcMarcheBottin/front/circuit-court/index.html.twig',
            ['hits' => $hits, 'css' => $css]
        );

        return new Response($html);

        return $this->downloadPdf($html, 'circuit-court.pdf');
    }
}