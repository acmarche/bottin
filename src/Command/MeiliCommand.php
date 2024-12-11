<?php

namespace AcMarche\Bottin\Command;

use AcMarche\Bottin\Search\MeiliServer;
use AcMarche\Bottin\Search\SearchMeili;
use AcMarche\Bottin\Tag\TagUtils;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
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
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('key', "key", InputOption::VALUE_NONE, 'Create a key');
        $this->addOption('tasks', "tasks", InputOption::VALUE_NONE, 'Display tasks');
        $this->addOption('reset', "reset", InputOption::VALUE_NONE, 'Search engine reset');
        $this->addOption('update', "update", InputOption::VALUE_NONE, 'Update data');
        $this->addArgument('keyword', InputArgument::OPTIONAL);
        $this->addArgument('latitude', InputArgument::OPTIONAL);
        $this->addArgument('longitude', InputArgument::OPTIONAL);
        $this->addArgument('distance', InputArgument::OPTIONAL);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $latitude = $input->getArgument('latitude');
        $longitude = $input->getArgument('longitude');
        $disance = (int)$input->getArgument('distance');
        $keyword = $input->getArgument('keyword');
        $key = (bool)$input->getOption('key');
        $tasks = (bool)$input->getOption('tasks');
        $reset = (bool)$input->getOption('reset');
        $update = (bool)$input->getOption('update');

        if ($key) {
            dump($this->meiliServer->createKey());

            return Command::SUCCESS;
        }

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

        if ($latitude && $longitude) {
            $response = $this->meilSearch->searchGeo2((float)$latitude, (float)$longitude, $disance);
            $facetDistribution = $response->getFacetDistribution();

            $this->displayResult($output, $response->getHits());

            return Command::SUCCESS;
        }

        if ($keyword) {
            $response = $this->meilSearch->doSearch($keyword);

            $this->displayResult($output, $response->getHits());
            $io->title($response->count() . ' results');
            $io->writeln($response->getQuery());
            $io->writeln($keyword . ' keyword given');

            return Command::SUCCESS;
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

    private function displayResult(OutputInterface $output, array $result): void
    {
        $data = [];
        foreach ($result as $hit) {
            $data[] = [
                'id' => $hit['id'],
                'name' => $hit['societe'],
                'localite' => $hit['localite'],
                'rue' => $hit['rue'],
            ];
        }
        $table = new Table($output);
        $table
            ->setHeaders(['Id', 'Name', 'Localité', 'Rue'])
            ->setRows($data);
        $table->render();
    }

}
