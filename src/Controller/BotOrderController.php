<?php

namespace App\Controller;

use App\Entity\BotOrders;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/bot-orders',name: 'admin_bot_orders_')]
final class BotOrderController extends AbstractController
{
    #[Route('/all', name: 'index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $title = 'Заказы с чат бота';
        $description = 'Просмотр заказов с бота телеграмма.';
        //Получаем все заявки с сущностии Заказов Телеграмм бота
        $botOrders = $entityManager->getRepository(BotOrders::class)->findAll();


        return $this->render('bot_order/index.html.twig', [
            'title' => $title,
            'orders' => $botOrders,
            'description' => $description,
        ]);
    }

}
