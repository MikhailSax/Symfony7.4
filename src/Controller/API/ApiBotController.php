<?php

namespace App\Controller\API;

use App\Entity\BotMessage;
use App\Entity\BotOrders;
use App\Repository\BotOrdersRepository;
use App\Service\API\BotService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1', name: 'api_')]
class ApiBotController extends AbstractController
{
    public function __construct(private readonly BotService $botService)
    {
    }

    #[Route('/telegram/auth', name: 'telegram_auth', methods: ['POST'])]
    public function auth(Request $request): JsonResponse
    {
        $auth = $this->resolveTelegramAuth($request);
        if ($auth instanceof JsonResponse) {
            return $auth;
        }

        return new JsonResponse([
            'success' => true,
            'user' => $auth,
        ]);
    }

    #[Route('/orders', name: 'orders_index', methods: ['GET'])]
    public function orders(BotOrdersRepository $botOrdersRepository): JsonResponse
    {
        return $this->json($botOrdersRepository->findBy([], ['id' => 'DESC']));
    }

    #[Route('/orders/create', name: 'orders_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $auth = $this->resolveTelegramAuth($request);
        if ($auth instanceof JsonResponse) {
            return $auth;
        }

        $data = json_decode($request->getContent(), true);

        if (!is_array($data)) {
            return new JsonResponse(['error' => 'Некорректный JSON'], 400);
        }

        if (empty($data['user_id']) || empty($data['phone'])) {
            return new JsonResponse(['error' => 'Не заполнены обязательные поля'], 400);
        }

        $task = $data['task'] ?? $data['product'] ?? null;
        if (!$task) {
            return new JsonResponse(['error' => 'Не передано описание заказа'], 400);
        }

        $order = (new BotOrders())
            ->setUserId((int) $data['user_id'])
            ->setUsername($data['user_name'] ?? null)
            ->setUserFirstName($data['user_firstName'] ?? null)
            ->setUserLastName($data['user_lastName'] ?? null)
            ->setCategory((string) ($data['category'] ?? 'Без категории'))
            ->setTask((string) $task)
            ->setPhone((string) $data['phone'])
            ->setStatus((string) ($data['status'] ?? 'new'))
            ->setCreatedAt(new \DateTimeImmutable());

        $em->persist($order);
        $em->flush();

        return new JsonResponse(['success' => true, 'order_id' => $order->getId()]);
    }

    #[Route('/message/create', name: 'message_create', methods: ['POST'])]
    public function createMessage(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $auth = $this->resolveTelegramAuth($request);
        if ($auth instanceof JsonResponse) {
            return $auth;
        }

        $data = json_decode($request->getContent(), true);
        if (!is_array($data)) {
            return new JsonResponse(['error' => 'Некорректный JSON'], 400);
        }

        if (!isset($data['message'])) {
            return new JsonResponse(['error' => 'Некорректная информация'], 400);
        }

        $contact = new BotMessage();

        $contact->setUserId((int) ($data['user_id'] ?? 0));
        $contact->setUsername($data['user_name'] ?? '');
        $contact->setUserFirstName($data['user_firstName'] ?? '');
        $contact->setUserLastName($data['user_lastName'] ?? '');
        $contact->setMessage((string) ($data['message'] ?? ''));
        $contact->setPhone((string) ($data['phone'] ?? ''));
        $contact->setCreatedAt(new \DateTimeImmutable());

        $em->persist($contact);
        $em->flush();

        return new JsonResponse(['success' => true, 'message_id' => $contact->getId()]);
    }

    /**
     * @return array<string, mixed>|JsonResponse
     */
    private function resolveTelegramAuth(Request $request): array|JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $initData = $request->headers->get('X-Telegram-Init-Data');
        if (!$initData && is_array($data) && isset($data['initData'])) {
            $initData = (string) $data['initData'];
        }

        if (!$initData) {
            return new JsonResponse(['error' => 'Telegram initData is required'], 401);
        }

        try {
            $validated = $this->botService->validateInitData($initData);
        } catch (\Throwable $e) {
            return new JsonResponse([
                'error' => 'Telegram auth failed',
                'details' => $e->getMessage(),
            ], 401);
        }

        return $validated['user'];
    }
}