<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Tests\Traits\UserTestTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserManagementControllerTest extends WebTestCase
{
    use UserTestTrait;

    public function testUserManagementRequiresAdminRole(): void
    {
        $client = static::createClient();

        $user = $this->createTestUser('user@example.com', ['ROLE_USER']);
        $client->loginUser($user);

        $client->request('GET', '/admin/users');

        $this->assertResponseStatusCodeSame(403);
    }

    public function testUserManagementIndexWithAdmin(): void
    {
        $client = static::createClient();

        $admin = $this->createTestUser('admin_users@example.com', ['ROLE_ADMIN']);
        $client->loginUser($admin);

        $crawler = $client->request('GET', '/admin/users');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', 'User Management');
        $this->assertSelectorExists('a:contains("Create New User")');
    }

    public function testCreateUserPageWithAdmin(): void
    {
        $client = static::createClient();

        $admin = $this->createTestUser('admin_create@example.com', ['ROLE_ADMIN']);
        $client->loginUser($admin);

        $crawler = $client->request('GET', '/admin/users/create');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', 'Create New User');
        $this->assertSelectorExists('input[name="email"]');
        $this->assertSelectorExists('select[name="role"]');
    }

    public function testCreateUserSubmission(): void
    {
        $client = static::createClient();

        $admin = $this->createTestUser('admin_submit@example.com', ['ROLE_ADMIN']);
        $client->loginUser($admin);

        $client->request('POST', '/admin/users/create', [
            'email' => 'newuser@example.com',
            'role' => 'USER',
        ]);

        $this->assertResponseRedirects('/admin/users');

        $crawler = $client->followRedirect();

        $this->assertSelectorExists('.alert-success:contains("User created successfully!")');
        $this->assertSelectorExists('.alert-warning:contains("Generated Password")');
    }

    public function testCreateUserWithInvalidEmail(): void
    {
        $client = static::createClient();

        $admin = $this->createTestUser('admin_invalid@example.com', ['ROLE_ADMIN']);
        $client->loginUser($admin);

        $client->request('POST', '/admin/users/create', [
            'email' => 'invalid-email',
            'role' => 'USER',
        ]);

        $this->assertResponseRedirects('/admin/users/create');

        $crawler = $client->followRedirect();

        $this->assertSelectorExists('.alert-danger:contains("valid email")');
    }

    public function testAdminSeesUserManagementInToolsDropdown(): void
    {
        $client = static::createClient();

        $admin = $this->createTestUser('admin_dropdown@example.com', ['ROLE_ADMIN']);
        $client->loginUser($admin);

        $crawler = $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('a[href="/admin/users"]:contains("User Management")');
    }

    public function testNonAdminDoesNotSeeUserManagementInToolsDropdown(): void
    {
        $client = static::createClient();

        $user = $this->createTestUser('user_dropdown@example.com', ['ROLE_USER']);
        $client->loginUser($user);

        $crawler = $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorNotExists('a[href="/admin/users"]');
    }
}
