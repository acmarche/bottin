<?php

namespace AcMarche\Bottin\Controller\Front;

use AcMarche\Bottin\Cap\CapService;
use AcMarche\Bottin\Search\SearchEngineInterface;
use AcMarche\Bottin\Tag\Repository\TagRepository;
use AcMarche\Bottin\Utils\PdfDownloaderTrait;
use AcMarche\Bottin\Utils\SortUtils;
use Gotenberg\Exceptions\GotenbergApiErrored;
use Gotenberg\Exceptions\NoOutputFileInResponse;
use Gotenberg\Gotenberg;
use Gotenberg\Stream;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ExportController extends AbstractController
{
    use PdfDownloaderTrait;

    public function __construct(
        #[Autowire('%kernel.project_dir%')]
        private string $project_dir,
        private readonly TagRepository $tagRepository,
        private readonly SearchEngineInterface $meilisearch,
        private ClientInterface $httpClient,
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

        $path = $this->project_dir.'/public/bottin.css';
        //$css = file_get_contents($path);
        $css = '';
        $html = $this->renderView(
            '@AcMarcheBottin/front/circuit-court/index.html.twig',
            ['hits' => $hits, 'css' => $css]
        );

        $apiUrl = 'http://localhost:3001';
        $urlPdf = 'https://bottin.marche.be/export/circuit-court';


        $client = HttpClient::create();
        $url = 'https://demo.gotenberg.dev/forms/chromium/convert/url';
        $params = [
            'url' => $urlPdf,
        ];
        $response = $client->request('POST', $url, [
            'body' => $params,
        ]);
        $content = $response->getContent();
        file_put_contents('my.pdf', $content);

        $status = $response->getStatusCode();
        if ($status !== Response::HTTP_OK) {
            // Handle error
        }

        $filesystem = new Filesystem();
        $filePath = $this->project_dir.'/var/cache/index.html';
        $filesystem->dumpFile($filePath, $html);

        $chromium = Gotenberg::chromium($apiUrl);
        $request = $chromium->pdf()->html(Stream::string($filePath, $html));
        $response = $this->httpClient->sendRequest($request);
        $stream = $response->getBody();
        dd($stream);

        $request = Gotenberg::chromium('http://localhost:3001')
            ->pdf()
            ->url('https://bottin.marche.be/export/circuit-court');

        try {
            $response = $this->httpClient->sendRequest($request);
            dump($response);
        } catch (ClientExceptionInterface $e) {
            dump($e->getMessage());
        }

        $stream = Stream::string('/tmp/t.txt', $html);
        try {
            $filename = Gotenberg::save(
                Gotenberg::chromium($apiUrl)->pdf()
                    ->html($stream),
                '/tmp'
            );
        } catch (GotenbergApiErrored|NoOutputFileInResponse $e) {
            dump($e->getMessage());
        }
        dd($filename);

        return new Response($html);

        return $this->downloadPdf($html, 'circuit-court.pdf');
    }
}