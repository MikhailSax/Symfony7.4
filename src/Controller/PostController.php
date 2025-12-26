<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


class PostController extends AbstractController
{
    #[Route('/posts', name: 'posts', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $posts = $entityManager->getRepository(Post::class)->findAll();

        return $this->render('post/index.html.twig', [
            'name' => 'post',
            'desc' => 'asd'
        ]);
    }

    #[Route('/post/create', name: 'post_create', methods: ['GET', 'POST'])]
    public function create(EntityManagerInterface $entityManager, Request $request): Response
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post, [
            'action' => $this->generateUrl('post_create'),
            'method' => 'POST'
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $post->setCreatedAt(new \DateTimeImmutable());
            $post->setUpdatedAt(new \DateTimeImmutable());

            $post = $form->getData();
            $entityManager->persist($post);
            $entityManager->flush();

            return $this->redirectToRoute('post_show', ['id' => $post->getId()]);
        }
        return $this->render('post/create.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('post/{id}', name: 'post_show', methods: ['GET'])]
    public function show(EntityManagerInterface $entityManager, int $id): Response
    {
        $post = $entityManager->getRepository(Post::class)->find($id);

        if (!$post) {
            throw $this->createNotFoundException(
                'No post found for id ' . $id
            );
        }

        return new Response('Пост с id: ' . $post->getId() . '\n' . ' Название поста: ' . $post->getName());
    }

    #[Route('post/delete/{id}', name: 'post_delete', methods: ['GET'])]
    public function delete(EntityManagerInterface $entityManager, int $id): Response
    {
        $product = $entityManager->getRepository(Post::class)->find($id);

        if (!$product) {
            throw $this->createNotFoundException('Пост с id для удаления не найден ' . $product->getId());
        } else {
            $entityManager->remove($product);
            $entityManager->flush();
            return new Response('Удаление объекта прошло успешно ' . $product->getId());
        }

    }
}
