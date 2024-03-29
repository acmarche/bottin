<?php

namespace AcMarche\Bottin\Command;

use AcMarche\Bottin\Search\SearchEngineInterface;
use Elasticsearch\Common\Exceptions\BadRequest400Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'bottin:search',
    description: 'Test search',
)]
class SearchCommand extends Command
{
    public function __construct(private readonly SearchEngineInterface $searchEngine, string $name = null)
    {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
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
        } catch (BadRequest400Exception $badRequest400Exception) {
            $io->error('Erreur dans la recherche: '.$badRequest400Exception->getMessage());
        }

        return 0;
    }
}
