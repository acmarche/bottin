<?php

namespace AcMarche\Bottin\Command;

use AcMarche\Bottin\Export\ExportUtils;
use AcMarche\Bottin\Mailer\MailFactory;
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
        private readonly ExportUtils $exportUtils,
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
            $message = null;
            $subject = 'Mise à jour de vos données';
            $message = $this->exportUtils->replaceUrlToken($fiche, $message);
            $email = $this->mailFactory->mailMessageToFiche($subject, $message, $fiche);
            try {
                //  $this->mailer->send($email);
            } catch (TransportExceptionInterface|\Exception $e) {
                $io->error("Erreur lors de l'envoie du message: ".$e->getMessage());
            }

            if (1 == $i) {
                // break;
            }

            ++$i;
        }

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }
}
