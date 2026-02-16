<?php

namespace App\Controller;

use App\Entity\BotOrders;
use App\Repository\BotOrdersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/bot-orders', name: 'admin_bot_orders_')]
final class BotOrderController extends AbstractController
{
    #[Route('/all', name: 'index', methods: ['GET'])]
    public function index(BotOrdersRepository $botOrdersRepository): Response
    {
        return $this->render('bot_order/index.html.twig', [
            'title' => 'Заказы с чат-бота',
            'description' => 'Просмотр и обработка заявок из Telegram mini app.',
            'orders' => $botOrdersRepository->findBy([], ['id' => 'DESC']),
        ]);
    }

    #[Route('/{id}/status', name: 'change_status', methods: ['POST'])]
    public function changeStatus(Request $request, BotOrders $order, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isCsrfTokenValid('status' . $order->getId(), $request->getPayload()->getString('_token'))) {
            throw $this->createAccessDeniedException('Некорректный CSRF токен');
        }

        $status = (string) $request->getPayload()->get('status', 'new');
        $allowedStatuses = ['new', 'in_progress', 'done'];

        if (!in_array($status, $allowedStatuses, true)) {
            $this->addFlash('danger', 'Неизвестный статус заказа.');
            return $this->redirectToRoute('admin_bot_orders_index');
        }

        $order->setStatus($status);
        $entityManager->flush();

        $this->addFlash('success', 'Статус заказа обновлён.');
        return $this->redirectToRoute('admin_bot_orders_index');
    }

    #[Route('/{id}/delete', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, BotOrders $order, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $order->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($order);
            $entityManager->flush();
            $this->addFlash('success', 'Заказ удалён.');
        }

        return $this->redirectToRoute('admin_bot_orders_index');
    }
}
