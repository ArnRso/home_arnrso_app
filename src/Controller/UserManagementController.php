<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\PasswordGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/users', name: 'app_admin_users_')]
class UserManagementController extends AbstractController
{
    #[Route('', name: 'index')]
    public function index(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();

        return $this->render('admin/users/index.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/create', name: 'create', methods: ['GET', 'POST'])]
    public function create(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
        PasswordGenerator $passwordGenerator
    ): Response {
        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');
            $role = $request->request->get('role');

            if (! is_string($email) || ! is_string($role)) {
                $this->addFlash('error', 'Invalid form data.');

                return $this->redirectToRoute('app_admin_users_create');
            }

            if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->addFlash('error', 'Please enter a valid email address.');

                return $this->redirectToRoute('app_admin_users_create');
            }

            $password = $passwordGenerator->generate();

            $user = new User();
            $user->setEmail($email);
            $user->setRoles(['ROLE_' . $role]);
            $user->setPassword($passwordHasher->hashPassword($user, $password));

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'User created successfully!');
            $this->addFlash('password', $password);

            return $this->redirectToRoute('app_admin_users_index');
        }

        return $this->render('admin/users/create.html.twig');
    }
}
