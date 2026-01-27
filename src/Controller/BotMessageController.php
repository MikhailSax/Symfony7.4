<?php

namespace App\Controller;

use App\Entity\BotMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('admin/bot-message/', name: 'admin_bot_message_')]
final class BotMessageController extends AbstractController
{
    #[Route('all', name: 'index')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $title = 'Заказы с чат бота';
        $description = 'Просмотр заказов с бота телеграмма.';
        $messages = $entityManager->getRepository(BotMessage::class)->findAll();

        return $this->render('bot_message/index.html.twig', [
            'messages' => $messages,
            'title' => $title,
            'description' => $description,
        ]);
    }

    #[Route('delete/{id}', name: 'delete', methods: ['POST'])]
    public function delete(EntityManagerInterface $entityManager,int $id): Response
    {
        $message = $entityManager->getRepository(BotMessage::class)->find($id);
        $entityManager->remove($message);
        $entityManager->flush();

        return $this->redirectToRoute('admin_bot_message_index',[
            'id' => $message->getUserId(),
            'message' => 'Сообщение було удаленно' . $id
        ]);
    }
}
