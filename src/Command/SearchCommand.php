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

    public function __construct(private SearchEngineInterface $searchEngine, string $name = null)
    {
        parent::__construct($name);
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
            $hits = $response->getResults();
            foreach ($hits as $hit) {
                $fiche = $hit->getData();
                $io->writeln($fiche['societe']);
            }
        } catch (BadRequest400Exception $e) {
            $io->error('Erreur dans la recherche: '.$e->getMessage());
        }

        return 0;
    }
}
