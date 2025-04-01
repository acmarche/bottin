<?php

namespace AcMarche\Bottin\Command;

use AcMarche\Bottin\Mailer\MailFactory;
use AcMarche\Bottin\Pdf\Factory\PdfFactory;
use AcMarche\Bottin\Repository\FicheRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;

#[AsCommand(
    name: 'bottin:publipostage',
    description: 'Add a short description for your command',
)]
class PublipostageCommand extends Command
{
    public function __construct(
        private readonly FicheRepository $ficheRepository,
        private readonly MailerInterface $mailer,
        private readonly MailFactory $mailFactory,
        private readonly PdfFactory $pdfFactory,
        string $name = null
    ) {
        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $fiches = $this->ficheRepository->findAll();
        $io = new SymfonyStyle($input, $output);
        $i = 0;
        foreach ($fiches as $fiche) {
            $body = $message = null;
            $subject = 'Mise à jour de vos données';
            $fileName = $this->pdfFactory->getFileName($fiche);
            if (!is_readable($fileName)) {
                $io->error("Pdf not found: ".$fiche->societe);
                continue;
            }
            try {
                $message = $this->mailFactory->mailMessageToFiche($subject, $body, $fiche, $fileName);
            } catch (\Exception $e) {
                $io->error('for email'.$fiche->societe.' '.$e->getMessage());
                continue;
            }
            try {
                $this->mailer->send($message);

            } catch (TransportExceptionInterface|\Exception $e) {
                $io->error("Erreur lors de l'envoie du message: ".$e->getMessage());
            }
            $io->writeln($i);
            ++$i;
        }

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }
}
