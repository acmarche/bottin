<?php

namespace AcMarche\Bottin\Command;

use AcMarche\Bottin\Elasticsearch\ElasticIndexer;
use AcMarche\Bottin\Repository\CategoryRepository;
use AcMarche\Bottin\Repository\FicheRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ElasticIndexerCommand extends Command
{
    protected static $defaultName = 'bottin:indexer';

    private ?SymfonyStyle $io = null;

    protected function configure(): void
    {
        $this
            ->setDescription('Mise à jour des données');
    }

    public function __construct(
        private ElasticIndexer $elasticIndexer,
        private FicheRepository $ficheRepository,
        private CategoryRepository $categoryRepository,
        string $name = null
    ) {
        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new SymfonyStyle($input, $output);

        $this->updateFiches();
        $this->updateCategories();

        return Command::SUCCESS;
    }

    private function updateFiches(): void
    {
        foreach ($this->ficheRepository->findAll() as $fiche) {
            $response = $this->elasticIndexer->updateFiche($fiche);
            if ($response->hasError()) {
                $this->io->error('Erreur lors de l\'indexation: '.$response->getErrorMessage());
            } else {
                $this->io->writeln($fiche->getSociete().': '.$response->getStatus());
            }
        }
    }

    private function updateCategories(): void
    {
        foreach ($this->categoryRepository->findAll() as $category) {
            $response = $this->elasticIndexer->updateCategorie($category);
            if ($response->hasError()) {
                $this->io->error('Erreur lors de l\'indexation: '.$response->getErrorMessage());
            } else {
                $this->io->writeln($category->getName().': '.$response->getStatus());
            }
        }
    }
}
