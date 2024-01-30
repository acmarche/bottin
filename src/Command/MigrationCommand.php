<?php

namespace AcMarche\Bottin\Command;

use AcMarche\Bottin\Entity\Tag;
use AcMarche\Bottin\Repository\FicheRepository;
use AcMarche\Bottin\Tag\Repository\TagRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'bottin:migration',
    description: 'Traitement par lot',
)]
class MigrationCommand extends Command
{
    public function __construct(
        private readonly FicheRepository $ficheRepository,
        private readonly TagRepository $tagRepository,
        string $name = null
    ) {
        parent::__construct($name);
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $symfonyStyle = new SymfonyStyle($input, $output);

        $types = [
            'centreville' => 'Centre-ville',
            'click_collect' => 'Click and collect',
            'pmr' => 'Pmr',
            'midi' => 'Temps de midi',
        ];

        foreach ($types as $key => $item) {
            $symfonyStyle->section($item);
            $tag = $this->tagRepository->findOneByName($item);
            if (!$tag instanceof Tag) {
                return Command::FAILURE;
            }

            foreach ($this->ficheRepository->findBy([$key => 1]) as $fiche) {
                $symfonyStyle->writeln($fiche->societe);

            $tag = $this->tagRepository->findOneByName($item);
                $fiche->addTag($tag);
            }
        }

     //   $this->tagRepository->flush();

        return Command::SUCCESS;
    }
}
