<?php

namespace App\Controller;

use App\Entity\Post;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class PostController extends AbstractController
{
    #[Route('/posts', name: 'posts', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $posts = $entityManager->getRepository(Post::class)->findAll();
        dd($posts);
        return $this->render('post/index.html.twig', [
            'name' => 'post',
            'desc' => 'asd'
        ]);
    }

    #[Route('/post/create', name: 'post_create', methods: ['GET'])]
    public function create(EntityManagerInterface $entityManager, ValidatorInterface $validator): Response
    {
        $post = new Post();
        $post->setName('Как правильно заказывать Календари');
        $post->setDescription('Календари заказываются лучше чем что то другое итд');
        $post->setUserId($this->getUser());
        $post->setCreatedAt(new \DateTimeImmutable());
        $post->setUpdatedAt(new \DateTimeImmutable());
        //Проверка объекта Поста
        $errors = $validator->validate($post);

        if (count($errors) > 0) {
            return new Response((string)$errors, 400);
        } else {
            $entityManager->persist($post);
            $entityManager->flush();

            return new Response('Saved new post with id' . $post->getId());
        }

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

        return new Response('Пост с id: ' . $post->getId() . ' Название поста: '. $post->getName());
    }
    #[Route('post/delete/{id}', name: 'post_delete', methods: ['GET'])]
    public function delete(EntityManagerInterface $entityManager, int $id): Response
    {
        $product = $entityManager->getRepository(Post::class)->find($id);

        if (!$product) {
            throw $this->createNotFoundException('Пост с id для удаления не найден '.$product->getId());
        } else {
            $entityManager->remove($product);
            $entityManager->flush();
            return new Response('Удаление объекта прошло успешно ' . $product->getId());
        }

    }
}
