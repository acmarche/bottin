<?php

namespace AcMarche\Bottin\Controller\Front;

use AcMarche\Bottin\Search\SearchMeili;
use AcMarche\Bottin\Tag\Repository\TagRepository;
use AcMarche\Bottin\Utils\PdfDownloaderTrait;
use AcMarche\Bottin\Utils\SortUtils;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ExportController
{
    use PdfDownloaderTrait;

    public function __construct(
        private readonly TagRepository $tagRepository,
        private readonly SearchMeili $meilisearch
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

        }

        $hits = SortUtils::sortArrayFiche($hits);

        $html = 'pdf avec liste';

      //  return new Response($html);

        return $this->downloadPdf($html, 'circuit-court.pdf');
    }
}