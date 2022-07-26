<?php

namespace AcMarche\Bottin\Hades\Command;

use AcMarche\Bottin\Entity\Category;
use AcMarche\Bottin\Hades\Hades;
use AcMarche\Bottin\Hades\HadesRepository;
use AcMarche\Bottin\Repository\CategoryRepository;
use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'bottin:syncftlb',
    description: 'Synchronise avec la ftlb',
)]
class SyncFtlbCommand extends Command
{
    private ?SymfonyStyle $symfonyStyle = null;

    public function __construct(
        private Hades $hades,
        private HadesRepository $hadesRepository,
        private CategoryRepository $categoryRepository
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->symfonyStyle = new SymfonyStyle($input, $output);
        $this->hades->desirialize();

        // $hotels = $this->hadesRepository->getHotels();
        //$this->importHotels();

        //  $output->writeln('ok');

        return 0;
    }

    protected function importHotels(): void
    {
        $category = $this->categoryRepository->find(Hades::CATEGORY_HOTELS);
        if (!$category instanceof Category) {
            return;
        }

        try {
            $hotels = $this->hadesRepository->getHotels();
            foreach ($hotels as $hotel) {
                //  $this->io->writeln($hotel->getTitre());
                //    $this->hadesImport->treatment($hotel, $categorie);
            }
        } catch (Exception $e) {
            $this->symfonyStyle->error($e->getMessage());
        }
    }
}
