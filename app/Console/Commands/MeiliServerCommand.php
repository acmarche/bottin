<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Search\MeiliServer;
use Illuminate\Console\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class MeiliServerCommand extends Command
{
    protected $signature = 'bottin:meili-server';

    protected $description = 'Create an index for legacy map';

    private readonly MeiliServer $meiliServer;

    protected function configure(): void
    {
        $this->setDescription('Manage server meilisearch');
        $this->addOption('key', 'key', InputOption::VALUE_NONE, 'Create a key');
        $this->addOption('tasks', 'tasks', InputOption::VALUE_NONE, 'Display tasks');
        $this->addOption('reset', 'reset', InputOption::VALUE_NONE, 'Search engine reset');
        $this->addOption('dump', 'dump', InputOption::VALUE_NONE, 'migrate data');
        $this->addOption('update', 'update', InputOption::VALUE_NONE, 'Update data');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $key = (bool) $input->getOption('key');
        $tasks = (bool) $input->getOption('tasks');
        $reset = (bool) $input->getOption('reset');
        $dump = (bool) $input->getOption('dump');
        $update = (bool) $input->getOption('update');

        $this->meiliServer = new MeiliServer();
        $this->meiliServer->initClientAndIndex();

        if ($key) {
            dump($this->meiliServer->createApiKey());

            return \Symfony\Component\Console\Command\Command::SUCCESS;
        }

        if ($tasks) {
            $this->tasks($output);

            return Command::SUCCESS;
        }

        if ($reset) {
            $this->meiliServer->createIndex();
            $this->meiliServer->settings();

            return Command::SUCCESS;
        }

        if ($update) {
            $this->meiliServer->addContent();
        }

        if ($dump) {
            dump($this->meiliServer->dump());
        }

        return Command::SUCCESS;
    }

    private function tasks(OutputInterface $output): void
    {
        $tasks = $this->meiliServer->client->getTasks();
        $data = [];
        foreach ($tasks->getResults() as $result) {
            $t = [$result['uid'], $result['status'], $result['type'], $result['startedAt']];
            $t['error'] = null;
            $t['url'] = null;
            if ($result['status'] === 'failed') {
                if (isset($result['error'])) {
                    $t['error'] = $result['error']['message'];
                    $t['link'] = $result['error']['link'];
                }
            }
            $data[] = $t;
        }
        $table = new Table($output);
        $table
            ->setHeaders(['Uid', 'status', 'Type', 'Date', 'Error', 'Url'])
            ->setRows($data);
        $table->render();
    }
}
