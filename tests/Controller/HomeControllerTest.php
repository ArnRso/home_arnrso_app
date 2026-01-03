<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Tests\Traits\UserTestTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomeControllerTest extends WebTestCase
{
    use UserTestTrait;

    public function testHomePageWithAuthenticatedUser(): void
    {
        $client = static::createClient();

        $user = $this->createTestUser('home_test@example.com', ['ROLE_USER']);
        $client->loginUser($user);

        $crawler = $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Welcome');
        $this->assertSelectorExists('nav.navbar');
        $this->assertSelectorTextContains('nav.navbar', 'Hello, home_test@example.com');
        $this->assertSelectorExists('a[href="/logout"]');
    }

    public function testNavbarContainsLogoutButton(): void
    {
        $client = static::createClient();

        $user = $this->createTestUser('admin@example.com', ['ROLE_ADMIN']);
        $client->loginUser($user);

        $crawler = $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.btn.btn-outline-light');
        $this->assertSelectorTextContains('.btn.btn-outline-light', 'Logout');
    }
}
