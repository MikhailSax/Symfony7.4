<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PostController extends AbstractController
{
    #[Route('/posts',name: 'posts',methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('post/index.html.twig',[
            'name' => 'post',
            'desc' => 'asd'
        ]);
    }
}
