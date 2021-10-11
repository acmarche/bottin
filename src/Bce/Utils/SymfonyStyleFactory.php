<?php

namespace AcMarche\Bottin\Bce\Utils;

use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Style\SymfonyStyle;

trait SymfonyStyleFactory
{
    private ?SymfonyStyle $symfonyStyle = null;
    private int $i = 0;

    private function create(): void
    {
        $input = new ArgvInput();
        $output = new ConsoleOutput();

        $this->symfonyStyle = new SymfonyStyle($input, $output);
    }

    public function writeLn(string $message)
    {
        if (!$this->symfonyStyle) {
            $this->create();
        }
        $this->symfonyStyle->writeln($message);
        ++$this->i;
    }
}
