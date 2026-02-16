<?php

namespace App\Tests\Service\API;

use App\Service\API\BotService;
use PHPUnit\Framework\TestCase;

class BotServiceTest extends TestCase
{
    public function testValidateInitDataWithValidSignature(): void
    {
        $botToken = '12345:token';
        $service = new BotService($botToken);

        $authDate = time();
        $userJson = '{"id":123456,"first_name":"Ivan","username":"ivan"}';

        $payload = [
            'auth_date' => (string) $authDate,
            'query_id' => 'AAEAAQ',
            'user' => $userJson,
        ];

        $hash = $this->makeHash($payload, $botToken);

        $initData = http_build_query([
            ...$payload,
            'hash' => $hash,
        ]);

        $validated = $service->validateInitData($initData);

        self::assertSame(123456, $validated['user']['id']);
        self::assertSame('ivan', $validated['user']['username']);
    }

    public function testValidateInitDataThrowsOnInvalidSignature(): void
    {
        $service = new BotService('12345:token');

        $initData = http_build_query([
            'auth_date' => (string) time(),
            'query_id' => 'AAEAAQ',
            'user' => '{"id":1}',
            'hash' => 'invalid',
        ]);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Telegram signature is invalid.');

        $service->validateInitData($initData);
    }

    /**
     * @param array<string, string> $payload
     */
    private function makeHash(array $payload, string $botToken): string
    {
        ksort($payload);

        $pairs = [];
        foreach ($payload as $key => $value) {
            $pairs[] = sprintf('%s=%s', $key, $value);
        }

        $dataCheckString = implode("\n", $pairs);
        $secretKey = hash_hmac('sha256', $botToken, 'WebAppData', true);

        return hash_hmac('sha256', $dataCheckString, $secretKey);
    }
}
