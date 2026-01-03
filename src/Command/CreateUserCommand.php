<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-user',
    description: 'Create a new user with a generated password',
)]
class CreateUserCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $helper = $this->getHelper('question');

        $io->title('Create a new user');

        $emailQuestion = new Question('Email address: ');
        $emailQuestion->setValidator(function ($answer) {
            if (!filter_var($answer, FILTER_VALIDATE_EMAIL)) {
                throw new \RuntimeException('Please enter a valid email address.');
            }
            return $answer;
        });
        $email = $helper->ask($input, $output, $emailQuestion);

        $roleQuestion = new ChoiceQuestion(
            'Select role (default: USER)',
            ['USER', 'ADMIN'],
            0
        );
        $role = $helper->ask($input, $output, $roleQuestion);

        $password = $this->generatePassword();

        $user = new User();
        $user->setEmail($email);
        $user->setRoles(['ROLE_' . $role]);
        $user->setPassword($this->passwordHasher->hashPassword($user, $password));

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $io->success('User created successfully!');
        $io->table(
            ['Field', 'Value'],
            [
                ['Email', $email],
                ['Role', $role],
                ['Generated Password', $password],
            ]
        );

        $io->warning('Please save this password securely. It will not be shown again.');

        return Command::SUCCESS;
    }

    private function generatePassword(int $length = 16): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%^&*';
        $password = '';
        $max = strlen($characters) - 1;

        for ($i = 0; $i < $length; $i++) {
            $password .= $characters[random_int(0, $max)];
        }

        return $password;
    }
}
