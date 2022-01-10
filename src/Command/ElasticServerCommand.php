<?php

namespace AcMarche\Bottin\Command;

use AcMarche\Bottin\Elasticsearch\ElasticServer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;

class ElasticServerCommand extends Command
{
    protected static $defaultName = 'bottin:server';

    public function __construct(private ElasticServer $elasticServer, string $name = null)
    {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Raz l\'index');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $helper = $this->getHelper('question');
        $question = new ConfirmationQuestion('Raz. Êtes vous sur ? (Y,N) ', false);

        if (!$helper->ask($input, $output, $question)) {
            return Command::SUCCESS;
        }

        $this->elasticServer->createIndex();
        $this->elasticServer->setMapping();

        $io->success('Index vidé');

        return Command::SUCCESS;
    }
}
