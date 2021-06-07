<?php

namespace AcMarche\Bottin\Hades\Command;

use AcMarche\Bottin\Hades\Hades;
use AcMarche\Bottin\Hades\HadesImport;
use AcMarche\Bottin\Hades\HadesRepository;
use AcMarche\Bottin\Repository\CategoryRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class SyncFtlbCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'bottin:syncftlb';
    private \AcMarche\Bottin\Hades\HadesRepository $hadesRepository;
    private \AcMarche\Bottin\Repository\CategoryRepository $categoryRepository;
    private ?\Symfony\Component\Console\Style\SymfonyStyle $symfonyStyle;
    private \AcMarche\Bottin\Hades\Hades $hades;

    public function __construct(
        Hades $hades,
        HadesRepository $hadesRepository,
        CategoryRepository $categoryRepository,
        string $name = null
    ) {
        parent::__construct($name);
        $this->hadesRepository = $hadesRepository;
        $this->categoryRepository = $categoryRepository;
        $this->hades = $hades;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Synchronise avec la ftlb');
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
        if (!$category) {
            return;
        }

        try {
            $hotels = $this->hadesRepository->getHotels();
            foreach ($hotels as $hotel) {
                //  $this->io->writeln($hotel->getTitre());
                //    $this->hadesImport->treatment($hotel, $categorie);
            }
        } catch (\Exception $e) {
            $this->symfonyStyle->error($e->getMessage());
        }
    }

}
