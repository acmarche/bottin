<?php

namespace AcMarche\Bottin\Command;

use AcMarche\Bottin\Bce\Bce;
use AcMarche\Bottin\Bce\Import\ImportHandler;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class BceImportCommand extends Command
{
    protected static $defaultName = 'bottin:bce-import';
    protected static $defaultDescription = 'Import bce csv files';
    private ImportHandler $importHandler;

    public function __construct(
        ImportHandler $importHandler,
        string $name = null
    ) {
        parent::__construct($name);
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

        if (!in_array($fileName, Bce::$files)) {
            $io->warning('Missing file name. Possible values: '.join(' ', Bce::$files));

            return Command::FAILURE;
        }

        if ($fileName === 'all') {
            $this->importHandler->importAll();

            return Command::SUCCESS;
        }

        try {
            $handler = $this->importHandler->loadHandlerByKey($fileName);
        } catch (\Exception $e) {
            $io->error($e->getMessage());

            return Command::FAILURE;
        }

        try {
            foreach ($handler->readFile($fileName) as $data) {
                $io->writeLn($handler->writeLn($data));
                $handler->handle($data);
                $io->writeln("Memory".xdebug_memory_usage());
            }
            $handler->flush();
        } catch (\Exception $e) {
            $io->error($e->getMessage());
        }

        return Command::SUCCESS;
    }
}
