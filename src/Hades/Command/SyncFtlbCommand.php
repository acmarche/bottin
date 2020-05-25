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
    protected static $defaultName = 'bottin:syncftlb';

    /**
     * @var HadesImport
     */
    private $hadesImport;
    /**
     * @var HadesRepository
     */
    private $hadesRepository;
    /**
     * @var CategoryRepository
     */
    private $categoryRepository;
    /**
     * @var SymfonyStyle
     */
    private $io;
    /**
     * @var Hades
     */
    private $hades;

    public function __construct(
        Hades $hades,
        HadesImport $hadesImport,
        HadesRepository $hadesRepository,
        CategoryRepository $categoryRepository,
        string $name = null
    ) {
        parent::__construct($name);
        $this->hadesImport = $hadesImport;
        $this->hadesRepository = $hadesRepository;
        $this->categoryRepository = $categoryRepository;
        $this->hades = $hades;
    }

    protected function configure()
    {
        $this
            ->setDescription('Synchronise avec la ftlb');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new SymfonyStyle($input, $output);
        $this->hades->desirialize();

       // $hotels = $this->hadesRepository->getHotels();
        //$this->importHotels();

        //  $output->writeln('ok');

        return 0;
    }

    protected function importHotels()
    {
        $categorie = $this->categoryRepository->find(Hades::CATEGORY_HOTELS);
        if (!$categorie) {
            return;
        }

        try {
            $hotels = $this->hadesRepository->getHotels();
            foreach ($hotels as $hotel) {
                //  $this->io->writeln($hotel->getTitre());
                //    $this->hadesImport->treatment($hotel, $categorie);
            }
        } catch (\Exception $e) {
            $this->io->error($e->getMessage());
        }
    }

}
