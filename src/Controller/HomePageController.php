<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomePageController extends AbstractController
{
    #[Route('/',
        name: 'home',
        methods: ['GET'],
        alias:['homepage']
    )]
    public function home(): Response
    {
        return $this->render('home/home.html.twig');
    }
}
