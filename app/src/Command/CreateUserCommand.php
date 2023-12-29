<?php

namespace App\Command;

use App\Entity\User;
use RuntimeException;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create:user',
    description: 'Add a short description for your command',
)]
class CreateUserCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly UserRepository $userRepository
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('username', InputArgument::REQUIRED, 'The username of the new user');
        $this->addArgument('password', InputArgument::OPTIONAL, 'The plain password of the new user');
        $this->addArgument('role', InputArgument::OPTIONAL, 'The role of the new user');
        $this->addArgument('email', InputArgument::OPTIONAL, 'The email of the new user');
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $username = $input->getArgument('username');

        if($this->userRepository->findOneBy(['username' => $username])){
            throw new RuntimeException(sprintf("An user with '$username' username already exists."));
        }

        $email = $input->getArgument('email');

        if($this->userRepository->findOneBy(['email' => $email])){
            throw new RuntimeException(sprintf("An user with '$email' email address already exists."));
        }

        $role = $input->getArgument('role');

        $plainPassword = $input->getArgument('password');

        if(!$plainPassword){
            $plainPassword = random_int(10000,99999);
        }
  
        $user = new User();
        $user->setUsername($username);
        $user->setRoles($role == 'admin' ? ['ROLE_ADMIN'] : ['ROLE_USER']);
        $user->setEmail($email);

        $hashedPassword = $this->passwordHasher->hashPassword($user,$plainPassword);
        $user->setPassword($hashedPassword);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        if($user->getId()){
            $io->success(sprintf("A new user '%s' has been created successfully with password '%s'", $username, $plainPassword));
            return Command::SUCCESS;
        }else{
            $io->error(sprintf('Error creating the new user. Please, try again.'));
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
