<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Tests\Traits\UserTestTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TamponTrackerControllerTest extends WebTestCase
{
    use UserTestTrait;

    public function testTamponTrackerPageRequiresAuthentication(): void
    {
        $client = static::createClient();
        $client->request('GET', '/tools/tampon-tracker');

        $this->assertResponseRedirects('/login');
    }

    public function testTamponTrackerPageWithAuthenticatedUser(): void
    {
        $client = static::createClient();

        $user = $this->createTestUser('tampon_test@example.com', ['ROLE_USER']);
        $client->loginUser($user);

        $crawler = $client->request('GET', '/tools/tampon-tracker');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', 'Tampon Tracker');
        $this->assertSelectorExists('button:contains("Insert")');
        $this->assertSelectorExists('button:contains("Remove")');
    }

    public function testTamponTrackerShowsNoDataInitially(): void
    {
        $client = static::createClient();

        $user = $this->createTestUser('tampon_initial@example.com', ['ROLE_USER']);
        $client->loginUser($user);

        $crawler = $client->request('GET', '/tools/tampon-tracker');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('.alert-info', 'No data yet');
    }

    public function testInsertTamponAction(): void
    {
        $client = static::createClient();

        $user = $this->createTestUser('tampon_insert@example.com', ['ROLE_USER']);
        $client->loginUser($user);

        $client->request('POST', '/tools/tampon-tracker/toggle', [
            'action' => 'inserted',
        ]);

        $this->assertResponseRedirects('/tools/tampon-tracker');

        $crawler = $client->followRedirect();

        $this->assertSelectorTextContains('.alert-success', 'Tampon inserted');
        $this->assertSelectorExists('.badge.bg-success:contains("Inserted")');
    }

    public function testRemoveTamponAction(): void
    {
        $client = static::createClient();

        $user = $this->createTestUser('tampon_remove@example.com', ['ROLE_USER']);
        $client->loginUser($user);

        $client->request('POST', '/tools/tampon-tracker/toggle', [
            'action' => 'inserted',
        ]);

        $client->request('POST', '/tools/tampon-tracker/toggle', [
            'action' => 'removed',
        ]);

        $this->assertResponseRedirects('/tools/tampon-tracker');

        $crawler = $client->followRedirect();

        $this->assertSelectorTextContains('.alert-warning', 'No tampon');
        $this->assertSelectorExists('.badge.bg-warning:contains("Removed")');
    }

    public function testHistoryShowsMultipleEvents(): void
    {
        $client = static::createClient();

        $user = $this->createTestUser('tampon_history@example.com', ['ROLE_USER']);
        $client->loginUser($user);

        $client->request('POST', '/tools/tampon-tracker/toggle', [
            'action' => 'inserted',
        ]);
        $client->request('POST', '/tools/tampon-tracker/toggle', [
            'action' => 'removed',
        ]);
        $client->request('POST', '/tools/tampon-tracker/toggle', [
            'action' => 'inserted',
        ]);

        $crawler = $client->request('GET', '/tools/tampon-tracker');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('h5:contains("History")');
        $this->assertCount(3, $crawler->filter('.list-group-item'));
    }

    public function testTamponTrackerInToolsDropdown(): void
    {
        $client = static::createClient();

        $user = $this->createTestUser('tampon_dropdown@example.com', ['ROLE_USER']);
        $client->loginUser($user);

        $crawler = $client->request('GET', '/tools/tampon-tracker');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('#toolsDropdown');
        $this->assertSelectorExists('a.dropdown-item[href="/tools/tampon-tracker"]');
    }
}
