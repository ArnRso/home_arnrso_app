<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class HomeControllerTest extends WebTestCase
{
    public function testHomePageWithAuthenticatedUser(): void
    {
        $client = static::createClient();
        $container = static::getContainer();

        $entityManager = $container->get(EntityManagerInterface::class);
        $passwordHasher = $container->get(UserPasswordHasherInterface::class);

        $testEmail = 'test_' . uniqid() . '@example.com';

        $user = new User();
        $user->setEmail($testEmail);
        $user->setRoles(['ROLE_USER']);
        $user->setPassword($passwordHasher->hashPassword($user, 'password'));

        $entityManager->persist($user);
        $entityManager->flush();

        $client->loginUser($user);

        $crawler = $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Welcome');
        $this->assertSelectorExists('nav.navbar');
        $this->assertSelectorTextContains('nav.navbar', 'Hello, ' . $testEmail);
        $this->assertSelectorExists('a[href="/logout"]');

        $entityManager->remove($user);
        $entityManager->flush();
    }

    public function testNavbarContainsLogoutButton(): void
    {
        $client = static::createClient();
        $container = static::getContainer();

        $entityManager = $container->get(EntityManagerInterface::class);
        $passwordHasher = $container->get(UserPasswordHasherInterface::class);

        $testEmail = 'admin_' . uniqid() . '@example.com';

        $user = new User();
        $user->setEmail($testEmail);
        $user->setRoles(['ROLE_ADMIN']);
        $user->setPassword($passwordHasher->hashPassword($user, 'password'));

        $entityManager->persist($user);
        $entityManager->flush();

        $client->loginUser($user);

        $crawler = $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.btn.btn-outline-light');
        $this->assertSelectorTextContains('.btn.btn-outline-light', 'Logout');

        $entityManager->remove($user);
        $entityManager->flush();
    }
}
