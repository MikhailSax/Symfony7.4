<?php

namespace App\Controller;

use App\Entity\Order;
use App\Form\OrderType;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/orders', name: 'admin_orders_')]
class OrderController extends AbstractController
{
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(Request $request, OrderRepository $orderRepository): Response
    {
        $status = $request->query->get('status');
        $orders = $status
            ? $orderRepository->findBy(['status' => $status], ['id' => 'DESC'])
            : $orderRepository->findBy([], ['id' => 'DESC']);

        return $this->render('order/index.html.twig', [
            'orders' => $orders,
            'statuses' => Order::STATUSES,
            'currentStatus' => $status,
        ]);
    }

    #[Route('/board', name: 'board', methods: ['GET'])]
    public function board(OrderRepository $orderRepository): Response
    {
        $columns = [];
        foreach (Order::STATUSES as $code => $label) {
            $columns[] = [
                'code' => $code,
                'label' => $label,
                'orders' => $orderRepository->findBy(['status' => $code], ['id' => 'DESC']),
            ];
        }

        return $this->render('order/board.html.twig', ['columns' => $columns]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $order = new Order();
        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($order);
            $entityManager->flush();

            return $this->redirectToRoute('admin_orders_index');
        }

        return $this->render('order/form.html.twig', [
            'form' => $form,
            'title' => 'Создание заказа',
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Order $order, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('admin_orders_index');
        }

        return $this->render('order/form.html.twig', [
            'form' => $form,
            'title' => sprintf('Редактирование заказа #%d', $order->getId()),
        ]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Order $order): Response
    {
        return $this->render('order/show.html.twig', [
            'order' => $order,
            'statuses' => Order::STATUSES,
        ]);
    }

    #[Route('/{id}/next-status', name: 'next_status', methods: ['POST'])]
    public function nextStatus(Order $order, EntityManagerInterface $entityManager): Response
    {
        $keys = array_keys(Order::STATUSES);
        $currentIndex = array_search($order->getStatus(), $keys, true);
        if ($currentIndex !== false && isset($keys[$currentIndex + 1])) {
            $order->setStatus($keys[$currentIndex + 1]);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_orders_board');
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Order $order, Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$order->getId(), $request->request->getString('_token'))) {
            $entityManager->remove($order);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_orders_index');
    }
}
