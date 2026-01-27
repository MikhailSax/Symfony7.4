<?php

namespace App\Controller\API;

use App\Entity\BotMessage;
use App\Entity\BotOrders;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1/', name: 'api_orders_')]
class ApiBotController extends AbstractController
{
    #[Route('orders/create', name: 'create_order', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['user_id'], $data['product'])) {
            return new JsonResponse(['error' => 'Некорректная информация'], 400);
        }

        $order = new BotOrders();
        $order->setUserId($data['user_id']);
        $order->setUsername($data['user_name']);
        $order->setCategory($data['category']);
        $order->setTask($data['product']);
        $order->setPhone($data['phone']);
        $order->setCreatedAt(new \DateTimeImmutable());

        $em->persist($order);
        $em->flush();

        return new JsonResponse(['success' => true, 'order_id' => $order->getId()]);
    }


    #[Route('message/create', name: 'create_message', methods: ['POST'])]
    public function createMessage(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $contact = new BotMessage();

        $contact->setUserId($data['user_id']);
        $contact->setUsername($data['user_name']);
        $contact->setUserFirstName($data['user_firstName']);
        $contact->setUserLastName($data['user_lastName']);
        $contact->setMessage($data['message']);
        $contact->setPhone($data['phone']);
        $contact->setCreatedAt(new \DateTimeImmutable());

        $em->persist($contact);
        $em->flush();



        return new JsonResponse(['success' => true, 'message_id' => $contact->getId()]);
    }
}
