<?php

namespace AcMarche\Bottin\Command;

use AcMarche\Bottin\Entity\User;
use AcMarche\Bottin\Repository\UserRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'bottin:create-user',
    description: 'Add a short description for your command',
)]
class CreateUserCommand extends Command
{
    public function __construct(
        private UserRepository $userRepository,
        private UserPasswordHasherInterface $userPasswordEncoder,
        string $name = null
    ) {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Création d\'un utilisateur')
            ->addArgument('name', InputArgument::REQUIRED, 'nom')
            ->addArgument('email', InputArgument::REQUIRED, 'Email')
            ->addArgument('password', InputArgument::REQUIRED, 'Mot de passe');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $symfonyStyle = new SymfonyStyle($input, $output);

        $email = $input->getArgument('email');
        $name = $input->getArgument('name');
        $password = $input->getArgument('password');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $symfonyStyle->error('Adresse email non valide');

            return 1;
        }

        if (\strlen($name) < 1) {
            $symfonyStyle->error('Name minium 1');

            return 1;
        }
        if (null !== $this->userRepository->findOneBy(['email' => $email])) {
            $symfonyStyle->error('Un utilisateur existe déjà avec cette adresse email');

            return 1;
        }

        $user = new User();
        $user->setUsername($email);
        $user->setEmail($email);
        $user->setNom($name);
        $user->setPassword($this->userPasswordEncoder->hashPassword($user, $password));
        $user->addRole('ROLE_BOTTIN_ADMIN');

        $this->userRepository->insert($user);

        $symfonyStyle->success('Utilisateur créé.');

        return Command::SUCCESS;
    }
}
