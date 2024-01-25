<?php

namespace AcMarche\Bottin\Command;

use AcMarche\Bottin\Entity\User;
use AcMarche\Bottin\Pdf\Factory\PdfFactory;
use AcMarche\Bottin\Repository\FicheRepository;
use AcMarche\Bottin\Repository\UserRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'bottin:generate-pdf',
    description: 'Génère les fiches en pdf',
)]
class PdfCommand extends Command
{
    public function __construct(
        private readonly FicheRepository $ficheRepository,
        private readonly PdfFactory $pdfFactory,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {

    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        foreach ($this->ficheRepository->findAll() as $fiche) {
            $fileName = $this->pdfFactory->getFileName($fiche);
            if (is_readable($fileName)) {
                continue;
            }
            try {
                $html = $this->pdfFactory->fiche($fiche);
                $this->pdfFactory->getPdf()->generateFromHtml($html, $fileName);
            } catch (\Exception $exception) {
                $io->error(
                    $fiche->getSlug()."Erreur lors de la création du pdf: ".$exception->getMessage()
                );
                continue;
            }
        }

        return Command::SUCCESS;
    }
}
