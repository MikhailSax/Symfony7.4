<?php

namespace App\Controller\API;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/v1/orders', name: 'api_orders_')]
class OrderBotController extends AbstractController
{
    #[Route('/create', name: 'create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['user_id'], $data['product'], $data['quantity'])) {
            return new JsonResponse(['error' => 'Invalid data'], 400);
        }

        $order = new Order();
        $order->setUserId($data['user_id']);
        $order->setProduct($data['product']);
        $order->setQuantity($data['quantity']);
        $order->setCreatedAt(new \DateTime());

        $em->persist($order);
        $em->flush();

        return new JsonResponse(['success' => true, 'order_id' => $order->getId()]);
    }
}
