<?php

declare(strict_types=1);

namespace App\Tests\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class CreateUserCommandTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;

    private string $testEmail;

    protected function setUp(): void
    {
        parent::setUp();
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $this->testEmail = 'test_' . uniqid() . '@example.com';
    }

    protected function tearDown(): void
    {
        $userRepository = $this->entityManager->getRepository(User::class);
        $user = $userRepository->findOneBy([
            'email' => $this->testEmail,
        ]);
        if ($user) {
            $this->entityManager->remove($user);
            $this->entityManager->flush();
        }

        parent::tearDown();
    }

    public function testCreateUserCommand(): void
    {
        $kernel = static::createKernel();
        $application = new Application($kernel);

        $command = $application->find('app:create-user');
        $commandTester = new CommandTester($command);
        $commandTester->setInputs([$this->testEmail, 'USER']);
        $commandTester->execute([]);

        $commandTester->assertCommandIsSuccessful();

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('User created successfully!', $output);
        $this->assertStringContainsString($this->testEmail, $output);
        $this->assertStringContainsString('USER', $output);
        $this->assertStringContainsString('Generated Password', $output);
    }
}
