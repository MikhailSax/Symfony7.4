<?php

namespace App\Controller\API;

use App\Entity\BotMessage;
use App\Entity\BotOrders;
use App\Service\API\BotService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1/', name: 'api_orders_')]
class ApiBotController extends AbstractController
{
    public function __construct(private readonly BotService $botService)
    {
    }

    #[Route('telegram/auth', name: 'telegram_auth', methods: ['POST'])]
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

    #[Route('orders/create', name: 'create_order', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $auth = $this->resolveTelegramAuth($request);
        if ($auth instanceof JsonResponse) {
            return $auth;
        }

        $data = json_decode($request->getContent(), true);

        if (!is_array($data) || !isset($data['product'])) {
            return new JsonResponse(['error' => 'Некорректная информация'], 400);
        }

        $order = new BotOrders();
        $order->setUserId((string) $auth['id']);
        $order->setUsername((string) ($auth['username'] ?? $auth['first_name'] ?? ''));
        $order->setCategory((string) ($data['category'] ?? ''));
        $order->setTask((string) $data['product']);
        $order->setPhone((string) ($data['phone'] ?? ''));
        $order->setCreatedAt(new \DateTimeImmutable());

        $em->persist($order);
        $em->flush();

        return new JsonResponse(['success' => true, 'order_id' => $order->getId()]);
    }

    #[Route('message/create', name: 'create_message', methods: ['POST'])]
    public function createMessage(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $auth = $this->resolveTelegramAuth($request);
        if ($auth instanceof JsonResponse) {
            return $auth;
        }

        $data = json_decode($request->getContent(), true);
        if (!is_array($data) || !isset($data['message'])) {
            return new JsonResponse(['error' => 'Некорректная информация'], 400);
        }

        $contact = new BotMessage();

        $contact->setUserId((string) $auth['id']);
        $contact->setUsername((string) ($auth['username'] ?? $auth['first_name'] ?? ''));
        $contact->setUserFirstName((string) ($auth['first_name'] ?? ''));
        $contact->setUserLastName((string) ($auth['last_name'] ?? ''));
        $contact->setMessage((string) $data['message']);
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
