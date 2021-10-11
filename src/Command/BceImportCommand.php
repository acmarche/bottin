<?php

namespace AcMarche\Bottin\Command;

use AcMarche\Bottin\Cbe\Import\CsvReader;
use AcMarche\Bottin\Cbe\Import\ImportHandler;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class BceImportCommand extends Command
{
    protected static $defaultName = 'bottin:bce-import';
    protected static $defaultDescription = 'Add a short description for your command';
    private CsvReader $csvReader;
    private ImportHandler $importHandler;

    public function __construct(
        CsvReader $csvReader,
        ImportHandler $importHandler,
        string $name = null
    ) {
        parent::__construct($name);
        $this->csvReader = $csvReader;
        $this->importHandler = $importHandler;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('fileName', InputArgument::REQUIRED, 'Argument description');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $fileName = $input->getArgument('fileName');

        try {
            $data = $this->csvReader->readFile($fileName);
        } catch (\Exception $e) {
            $io->error($e->getMessage());

            return Command::FAILURE;
        }

        try {
            $handler = $this->importHandler->loadInterfaceByKey($fileName);
            $handler->handle($data);

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $io->error($e->getMessage());

            return Command::FAILURE;
        }


    }
}
