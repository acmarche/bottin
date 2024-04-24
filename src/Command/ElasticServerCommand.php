<?php

namespace AcMarche\Bottin\Command;

use AcMarche\Bottin\Elasticsearch\ElasticServer;
use Elastic\Elasticsearch\Exception\AuthenticationException;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\MissingParameterException;
use Elastic\Elasticsearch\Exception\ServerResponseException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'bottin:elastic',
    description: "Manage index",
)]
class ElasticServerCommand extends Command
{
    public function __construct(private readonly ElasticServer $elasticServer, string $name = null)
    {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this->addOption('reset', "reset", InputOption::VALUE_NONE, 'Search engine reset');
        $this->addOption('update', "update", InputOption::VALUE_NONE, 'Update data');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $reset = (bool)$input->getOption('reset');
        $update = (bool)$input->getOption('update');

        if ($reset) {
            try {
                $result = $this->elasticServer->reset();
                dump($result);
            } catch (ClientResponseException|ServerResponseException|MissingParameterException|AuthenticationException $e) {
                $io->error($e->getMessage());
            }
        }

        if ($update) {
            try {
                $this->elasticServer->addAll();
            } catch (AuthenticationException|ClientResponseException|MissingParameterException|ServerResponseException $e) {
                $io->error($e->getMessage());
            }
        }

        return Command::SUCCESS;
    }
}
