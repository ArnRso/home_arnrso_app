<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ToolControllerTest extends WebTestCase
{
    public function testCrossProductCalculatorPageRequiresAuthentication(): void
    {
        $client = static::createClient();
        $client->request('GET', '/tools/cross-product');

        $this->assertResponseRedirects('/login');
    }

    public function testCrossProductCalculatorPageWithAuthenticatedUser(): void
    {
        $client = static::createClient();
        $container = static::getContainer();

        $entityManager = $container->get(EntityManagerInterface::class);
        $passwordHasher = $container->get(UserPasswordHasherInterface::class);

        $testEmail = 'tool_test_' . uniqid() . '@example.com';

        $user = new User();
        $user->setEmail($testEmail);
        $user->setRoles(['ROLE_USER']);
        $user->setPassword($passwordHasher->hashPassword($user, 'password'));

        $entityManager->persist($user);
        $entityManager->flush();

        $client->loginUser($user);

        $crawler = $client->request('GET', '/tools/cross-product');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', 'Cross Product Calculator');
        $this->assertSelectorExists('#crossProductForm');
        $this->assertSelectorExists('#valueA');
        $this->assertSelectorExists('#valueB');
        $this->assertSelectorExists('#valueC');
        $this->assertSelectorExists('#resultX');

        $entityManager->remove($user);
        $entityManager->flush();
    }

    public function testCrossProductCalculatorHasToolsDropdownInNavbar(): void
    {
        $client = static::createClient();
        $container = static::getContainer();

        $entityManager = $container->get(EntityManagerInterface::class);
        $passwordHasher = $container->get(UserPasswordHasherInterface::class);

        $testEmail = 'tool_navbar_' . uniqid() . '@example.com';

        $user = new User();
        $user->setEmail($testEmail);
        $user->setRoles(['ROLE_USER']);
        $user->setPassword($passwordHasher->hashPassword($user, 'password'));

        $entityManager->persist($user);
        $entityManager->flush();

        $client->loginUser($user);

        $crawler = $client->request('GET', '/tools/cross-product');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('#toolsDropdown');
        $this->assertSelectorTextContains('#toolsDropdown', 'Tools');

        $entityManager->remove($user);
        $entityManager->flush();
    }
}
