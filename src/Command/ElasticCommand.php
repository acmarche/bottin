<?php

namespace AcMarche\Bottin\Command;

use AcMarche\Bottin\Elastic\ElasticServer;
use AcMarche\Bottin\Repository\CategoryRepository;
use AcMarche\Bottin\Repository\FicheRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * https://medium.com/@stefan.poeltl/symfony-meets-elasticsearch-implement-a-search-as-you-type-feature-307e2244f078.
 */
class ElasticCommand extends Command
{
    protected static $defaultName = 'bottin:elastic';
    private ElasticServer $elasticServer;
    private FicheRepository $ficheRepository;
    private CategoryRepository $categoryRepository;
    private ?SymfonyStyle $symfonyStyle;

    public function __construct(
        ElasticServer $elasticServer,
        FicheRepository $ficheRepository,
        CategoryRepository $categoryRepository,
        string $name = null
    ) {
        parent::__construct($name);
        $this->elasticServer = $elasticServer;
        $this->ficheRepository = $ficheRepository;
        $this->categoryRepository = $categoryRepository;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Manipule l\'index du bottin')
            ->addOption('raz', null, InputOption::VALUE_NONE, 'Mets à jour le mapping')
            ->addOption('mapping', null, InputOption::VALUE_NONE, 'Mets à jour le mapping')
            ->addOption('update', null, InputOption::VALUE_NONE, 'Mets à jour le contenu');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->symfonyStyle = new SymfonyStyle($input, $output);

        if ($input->getOption('raz')) {
            $this->elasticServer->razIndex();
        }

        if ($input->getOption('mapping')) {
            $this->elasticServer->updateSettingAndMapping();
        }

        if ($input->getOption('update')) {
            $this->updateFiches();
            $this->updateCategories();
        }

        return 0;
    }

    private function updateFiches(): void
    {
        foreach ($this->ficheRepository->findAll() as $fiche) {
            $result = $this->elasticServer->updateFiche($fiche);
            if (1 == $result['_shards']['successful']) {
                $this->symfonyStyle->success($fiche->getSociete().': '.$result['result']);
            }
            if (1 == $result['_shards']['failed']) {
                $this->symfonyStyle->error($fiche->getSociete());
                $this->symfonyStyle->error(var_export($result));
            }
        }
    }

    private function updateCategories(): void
    {
        foreach ($this->categoryRepository->findAll() as $category) {
            $result = $this->elasticServer->updateCategorie($category);
            if (1 == $result['_shards']['successful']) {
                $this->symfonyStyle->success($category->getName().': '.$result['result']);
            }
            if (1 == $result['_shards']['failed']) {
                $this->symfonyStyle->error(var_export($result));
            }
        }
    }
}
