<?php

namespace App\Controller;

use App\Service\MessageGenerator;
use Psr\Log\LoggerInterface;
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
    public function home(LoggerInterface $logger,MessageGenerator $message): Response
    {
        $message = $message->getMessage();
        $logger->info('Вы посетили главную страницу!');
        return $this->render('home/home.html.twig',[
            'message' => $message,
        ]);
    }
}
