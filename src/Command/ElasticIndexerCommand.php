<?php

namespace AcMarche\Bottin\Command;

use AcMarche\Bottin\Elasticsearch\ElasticIndexer;
use AcMarche\Bottin\Repository\CategoryRepository;
use AcMarche\Bottin\Repository\FicheRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'bottin:indexer',
    description: 'Mise à jour des données',
)]
class ElasticIndexerCommand extends Command
{
    private ?SymfonyStyle $io = null;

    private array $skips = [705];

    public function __construct(
        private readonly ElasticIndexer $elasticIndexer,
        private readonly FicheRepository $ficheRepository,
        private readonly CategoryRepository $categoryRepository,
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
        foreach ($this->ficheRepository->findAllWithJoins() as $fiche) {
            $skip = false;
            foreach ($this->skips as $categoryId) {
                if ($fiche->hasCategory($categoryId)) {
                    $skip = true;
                }
            }

            if ($skip) {
                continue;
            }

            $response = $this->elasticIndexer->updateFiche($fiche);
            if ($response->hasError()) {
                $this->io->error("Erreur lors de l'indexation: ".$response->getErrorMessage());
            } else {
                $this->io->writeln($fiche->societe.': '.$response->getStatus());
            }
        }
    }

    private function updateCategories(): void
    {
        foreach ($this->categoryRepository->findAll() as $category) {
            if (\in_array($category->getId(), $this->skips, true)) {
                continue;
            }

            $response = $this->elasticIndexer->updateCategorie($category);
            if ($response->hasError()) {
                $this->io->error("Erreur lors de l'indexation: ".$response->getErrorMessage());
            } else {
                $this->io->writeln($category->name.': '.$response->getStatus());
            }
        }
    }
}
