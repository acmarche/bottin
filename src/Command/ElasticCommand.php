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

class ElasticCommand extends Command
{
    protected static $defaultName = 'bottin:elastic';
    /**
     * @var ElasticServer
     */
    private $elasticServer;
    /**
     * @var FicheRepository
     */
    private $ficheRepository;
    /**
     * @var CategoryRepository
     */
    private $categoryRepository;
    /**
     * @var SymfonyStyle
     */
    private $io;

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

    protected function configure()
    {
        $this
            ->setDescription('Manipule l\'index du bottin')
            ->addOption('raz', null, InputOption::VALUE_NONE, 'Mets à jour le mapping')
            ->addOption('mapping', null, InputOption::VALUE_NONE, 'Mets à jour le mapping')
            ->addOption('update', null, InputOption::VALUE_NONE, 'Mets à jour le contenu');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new SymfonyStyle($input, $output);

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

    private function updateFiches()
    {
        foreach ($this->ficheRepository->findAll() as $fiche) {
            $result = $this->elasticServer->updateFiche($fiche);
            if ($result['_shards']['successful'] == 1) {
                $this->io->success($fiche->getSociete().': '.$result['result']);
            }
            if ($result['_shards']['failed'] == 1) {
                $this->io->error($fiche->getSociete());
                var_dump($result);
            }
        }
    }

    private function updateCategories()
    {
        foreach ($this->categoryRepository->findAll() as $category) {
            $result = $this->elasticServer->updateCategorie($category);
            if ($result['_shards']['successful'] == 1) {
                $this->io->success($category->getName().': '.$result['result']);
            }
            if ($result['_shards']['failed'] == 1) {
                $this->io->error($category->getName());
                var_dump($result);
            }
        }
    }
}
