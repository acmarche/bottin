<?php

namespace AcMarche\Bottin\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Routing\RouterInterface;

class SearchCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'bottin:search';

    private \Symfony\Component\Routing\RouterInterface $router;
    private ?\Symfony\Component\Console\Style\SymfonyStyle $symfonyStyle;

    public function __construct(RouterInterface $router, string $name = null)
    {
        parent::__construct($name);
        $this->router = $router;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Test search')
            ->addArgument('keyword', InputArgument::OPTIONAL, 'Mot clef');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->symfonyStyle = new SymfonyStyle($input, $output);

        $keyword = 'boulangerie';

        $dataNew = $this->searchNew($keyword);
        $result = $dataNew['hits'];
        $this->symfonyStyle->writeln('Trouvé: '.$result['total']['value']);

        foreach ($result['hits'] as $hit) {
            $source = $hit['_source'];
            $this->symfonyStyle->writeln('Trouvé: '.$source['societe'].' cap '.$source['cap']);
        }

        return 0;
    }

    protected function searchNew(string $keyword)
    {
        $httpClient = HttpClient::create(
            [
                'auth_basic' => ['**', '**'],
            ]
        );

        $data = ['keyword' => $keyword];

        $url = $this->router->generate('bottin_api_search', [], false);
        $request = $httpClient->request(
            "POST",
            $url,
            [
                'body' => $data,
            ]
        );

        return json_decode($request->getContent(), true);
    }

}
