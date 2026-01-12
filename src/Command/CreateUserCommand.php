<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\User;
use App\Service\PasswordGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AsCommand(name: 'app:create-user', description: 'Create a new user with a generated password',)]
class CreateUserCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly PasswordGenerator $passwordGenerator,
        private readonly TranslatorInterface $translator,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title($this->translator->trans('command.create_user.title'));

        $emailQuestion = new Question($this->translator->trans('command.create_user.email_prompt'));
        $emailQuestion->setValidator(function ($answer) {
            if (! filter_var($answer, FILTER_VALIDATE_EMAIL)) {
                throw new \RuntimeException($this->translator->trans('command.create_user.email_validation'));
            }
            return $answer;
        });
        $email = $io->askQuestion($emailQuestion);

        $roleQuestion = new ChoiceQuestion($this->translator->trans('command.create_user.role_prompt'), [
            'USER',
            'ADMIN',
        ], 0);
        $role = $io->askQuestion($roleQuestion);

        $password = $this->passwordGenerator->generate();

        $user = new User();
        $user->setEmail($email);
        $user->setRoles(['ROLE_' . $role]);
        $user->setPassword($this->passwordHasher->hashPassword($user, $password));

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $io->success($this->translator->trans('command.create_user.success'));
        $io->table(
            [
                $this->translator->trans('command.create_user.field'),
                $this->translator->trans('command.create_user.value'),
            ],
            [
                [$this->translator->trans('command.create_user.email_field'), $email],
                [$this->translator->trans('command.create_user.role_field'), $role],
                [$this->translator->trans('command.create_user.password_field'), $password],
            ]
        );

        $io->warning($this->translator->trans('command.create_user.password_warning'));

        return Command::SUCCESS;
    }
}
