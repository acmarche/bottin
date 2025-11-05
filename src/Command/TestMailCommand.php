<?php

namespace AcMarche\Bottin\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

#[AsCommand(
    name: 'bottin:test-mail',
    description: 'Add a short description for your command',
)]
class TestMailCommand extends Command
{
    public function __construct(private readonly MailerInterface $mailer)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('from', InputArgument::REQUIRED, 'Argument description')
            ->addArgument('to', InputArgument::REQUIRED, 'Argument description');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $from = $input->getArgument('from');
        $email = $input->getArgument('to');

        if ($email) {
            $io->info(sprintf('Try from %s to %s', $from, $email));
            $message = (new Email())
                ->from($from)
                ->to($email)
                ->subject('Time for Symfony Mailer!')
                ->text('Sending emails is fun again!')
                ->html('<p><a href="bankapp://transfer?amount=50.00&currency=EUR&iban=BE12345678901234&reference=Invoice123">qrcode</a>
See Twig integration for better HTML integration!</p>');

            try {
                $this->mailer->send($message);
                $io->note(sprintf('Message sended: %s', $email));
            } catch (TransportExceptionInterface|\Exception $e) {
                $io->error($e->getMessage());
            }
        }

        return Command::SUCCESS;
    }
}
