<?php

namespace AcMarche\Bottin\Command;

use AcMarche\Bottin\Entity\Fiche;
use AcMarche\Bottin\Mailer\MailFactory;
use AcMarche\Bottin\Repository\HistoryRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;

#[AsCommand(
    name: 'bottin:send-history',
    description: 'Add a short description for your command',
)]
class SendHistoryCommand extends Command
{
    public function __construct(
        private readonly HistoryRepository $historyRepository,
        private readonly MailFactory $mailFactory,
        private readonly MailerInterface $mailer,
        string $name = null
    ) {
        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $today = new \DateTime('-1 day');

        $changes = [];
        $deleted = [];
        $histories = $this->historyRepository->findModifiedByToken($today->format('Y-m-d'));
        foreach ($histories as $history) {
            $fiche = $history->fiche;
            if ($fiche instanceof Fiche) {
                $ficheId = $fiche->getId();
                $changes[$ficheId][] = $history;
            } else {
                $deleted[] = $history->old_value;
            }
        }

        if ([] !== $changes) {
            $email = $this->mailFactory->mailHistory($changes);
            try {
                $this->mailer->send($email);
            } catch (TransportExceptionInterface $e) {
                $io->error('Erreur envoie changement '.$e->getMessage());
            }
        }

        return Command::SUCCESS;
    }
}
