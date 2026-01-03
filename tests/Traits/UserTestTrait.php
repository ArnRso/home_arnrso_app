<?php

declare(strict_types=1);

namespace App\Tests\Traits;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

trait UserTestTrait
{
    /**
     * @param list<string> $roles
     */
    private function createTestUser(
        string $email,
        array $roles = ['ROLE_USER'],
        string $password = 'password'
    ): User {
        $container = static::getContainer();

        $entityManager = $container->get(EntityManagerInterface::class);
        $passwordHasher = $container->get(UserPasswordHasherInterface::class);

        $user = new User();
        $user->setEmail($email);
        $user->setRoles($roles);
        $user->setPassword($passwordHasher->hashPassword($user, $password));

        $entityManager->persist($user);
        $entityManager->flush();

        return $user;
    }
}
