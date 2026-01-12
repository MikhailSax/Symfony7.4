<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('admin/users', name: 'admin_users_')]
final class UserController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $users = $entityManager->getRepository(User::class)->findAll();

        return $this->render('user/index.html.twig', [
            'users' => $users,
            'title' => 'Управление пользвоателями'
        ]);
    }

    #[Route('/create', name: 'app_user_create')]
    public function create(EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $user->setName('John Doe');
        $user->setLastName('Bryanskiy');
        $user->setBirthDate(new \DateTime());
        $user->setCreatedAt(new \DateTimeImmutable());
        $user->setUpdatedAt(new \DateTimeImmutable());

        $entityManager->persist($user);
        $entityManager->flush();

        return new Response('Saved new user with id' .$user->getId());

    }
}
