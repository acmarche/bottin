<?php

namespace AcMarche\Bottin\Command;

use AcMarche\Bottin\Search\MeiliServer;
use AcMarche\Bottin\Search\SearchMeili;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'bottin:meili',
    description: 'Mise à jour du moteur de recherche',
)]
class MeiliCommand extends Command
{
    public function __construct(
        private readonly MeiliServer $meiliServer,
        private readonly SearchMeili $meilSearch,
        string $name = null
    ) {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this->addOption('tasks', "tasks", InputOption::VALUE_NONE, 'Display tasks');
        $this->addOption('reset', "reset", InputOption::VALUE_NONE, 'Search engine reset');
        $this->addOption('update', "update", InputOption::VALUE_NONE, 'Update data');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $tasks = (bool)$input->getOption('tasks');
        $reset = (bool)$input->getOption('reset');
        $update = (bool)$input->getOption('update');

        if ($tasks) {
            $this->tasks($output);

            return Command::SUCCESS;
        }

        if ($reset) {
            $result = $this->meiliServer->createIndex();
            dump($result);
            $result = $this->meiliServer->settings();
            dump($result);
        }

        if ($update) {
            $this->meiliServer->addContent();
        }

        return Command::SUCCESS;
    }

    private function tasks(OutputInterface $output): void
    {
        $this->meilSearch->init();
        $tasks = $this->meilSearch->client->getTasks();
        $data = [];
        foreach ($tasks->getResults() as $result) {
            $t = [$result['uid'], $result['status'], $result['type'], $result['startedAt']];
            $t['error'] = null;
            $t['url'] = null;
            if ($result['status'] == 'failed') {
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
