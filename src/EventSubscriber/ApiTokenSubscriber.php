<?php


namespace App\EventSubscriber;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class ApiTokenSubscriber implements EventSubscriberInterface
{
    public function __construct(
        #[Autowire(env: 'API_ACCESS_TOKEN')]
        private string $apiToken
    )
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            RequestEvent::class => 'onRequest',
        ];
    }

    public function onRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        // защищаем только API
        if (!str_starts_with($request->getPathInfo(), '/api/')) {
            return;
        }

        $auth = $request->headers->get('Authorization');

        if ($auth !== 'Bearer ' . $this->apiToken) {
            $event->setResponse(
                new JsonResponse(['error' => 'Unauthorized'], 401)
            );
        }
    }
}
