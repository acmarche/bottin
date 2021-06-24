<?php

namespace AcMarche\Bottin\Command;

use AcMarche\Bottin\Search\SearchEngineInterface;
use Elasticsearch\Common\Exceptions\BadRequest400Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class SearchCommand extends Command
{
    protected static $defaultName = 'bottin:search';
    private SearchEngineInterface $searchEngine;

    public function __construct(SearchEngineInterface $searchEngine, string $name = null)
    {
        parent::__construct($name);
        $this->searchEngine = $searchEngine;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Test search')
            ->addArgument('keyword', InputArgument::REQUIRED, 'Mot clef');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $keyword = $input->getArgument('keyword');

        try {
            $response = $this->searchEngine->doSearch($keyword);
            $hits = $response['hits'];
            foreach ($hits as $hit) {
                var_dump($hit);
            }
        } catch (BadRequest400Exception $e) {
            $io->error('Erreur dans la recherche: '.$e->getMessage());
        }

        return 0;
    }
}
