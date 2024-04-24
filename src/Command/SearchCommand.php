<?php

namespace AcMarche\Bottin\Command;

use AcMarche\Bottin\Search\SearchElastic;
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
    public function __construct(
        private readonly SearchElastic $searchElastic,
        string $name = null
    ) {
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
        $io->writeln('Keywword: '.$keyword);
        try {
            $result = $this->searchElastic->search($keyword);
            $count = $result->asObject()->hits->total->value;
            $io->writeln('count '.$count);
            foreach ($result->asObject()->hits->hits as $hit) {
                $io->writeln($hit->_source->societe);
                if ($hit->_source->email) {
                    $io->writeln($hit->_source->email);
                }
            }

            return Command::SUCCESS;
        } catch (\Exception $badRequest400Exception) {
            $io->error('Erreur dans la recherche: '.$badRequest400Exception->getMessage());
        }

        return 0;
    }
}
