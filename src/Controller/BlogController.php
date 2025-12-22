<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/blog',
    name: 'blog_',
    requirements: ['_locale' => 'ru|en']
)]
class BlogController extends AbstractController
{
    #[Route('/{_locale}', name: 'index')]
    public function index(int $page, string $title): Response
    {
        return $this->render('blog/index.html.twig', [
            'title' => $title,
            'page' => $page,
        ]);
    }

    #[Route('/{_locale}/posts/{id}',
        name: 'show',
        defaults: ['id' => 1],
        methods: ['GET']
    )]
    public function show(int $id): Response
    {
        return $this->render('blog/show.html.twig',[
            'id' => $id,
        ]);
    }


}
