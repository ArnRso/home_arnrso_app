<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Tests\Traits\UserTestTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ToolControllerTest extends WebTestCase
{
    use UserTestTrait;

    public function testCrossProductCalculatorPageRequiresAuthentication(): void
    {
        $client = static::createClient();
        $client->request('GET', '/tools/cross-product');

        $this->assertResponseRedirects('/login');
    }

    public function testCrossProductCalculatorPageWithAuthenticatedUser(): void
    {
        $client = static::createClient();

        $user = $this->createTestUser('tool_test@example.com', ['ROLE_USER']);
        $client->loginUser($user);

        $crawler = $client->request('GET', '/tools/cross-product');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', 'Cross Product Calculator');
        $this->assertSelectorExists('#crossProductForm');
        $this->assertSelectorExists('#valueA');
        $this->assertSelectorExists('#valueB');
        $this->assertSelectorExists('#valueC');
        $this->assertSelectorExists('#resultX');
    }

    public function testCrossProductCalculatorHasToolsDropdownInNavbar(): void
    {
        $client = static::createClient();

        $user = $this->createTestUser('tool_navbar@example.com', ['ROLE_USER']);
        $client->loginUser($user);

        $crawler = $client->request('GET', '/tools/cross-product');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('#toolsDropdown');
        $this->assertSelectorTextContains('#toolsDropdown', 'Tools');
    }
}
