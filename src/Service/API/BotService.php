<?php

namespace App\Service\API;

final class BotService
{
    private const MAX_AUTH_AGE = 86400;

    public function __construct(private readonly string $botToken)
    {
    }

    /**
     * @return array<string, mixed>
     */
    public function validateInitData(string $initData): array
    {
        if ($this->botToken === '') {
            throw new \RuntimeException('TELEGRAM_BOT_TOKEN is not configured.');
        }

        parse_str($initData, $payload);

        if (!isset($payload['hash'])) {
            throw new \InvalidArgumentException('Telegram hash is missing.');
        }

        $telegramHash = (string) $payload['hash'];
        unset($payload['hash']);

        ksort($payload);

        $pairs = [];
        foreach ($payload as $key => $value) {
            $pairs[] = sprintf('%s=%s', $key, $value);
        }

        $dataCheckString = implode("\n", $pairs);
        $secretKey = hash_hmac('sha256', $this->botToken, 'WebAppData', true);
        $calculatedHash = hash_hmac('sha256', $dataCheckString, $secretKey);

        if (!hash_equals($calculatedHash, $telegramHash)) {
            throw new \InvalidArgumentException('Telegram signature is invalid.');
        }

        if (!isset($payload['auth_date'])) {
            throw new \InvalidArgumentException('Telegram auth_date is missing.');
        }

        if ((time() - (int) $payload['auth_date']) > self::MAX_AUTH_AGE) {
            throw new \InvalidArgumentException('Telegram init data is expired.');
        }

        if (!isset($payload['user'])) {
            throw new \InvalidArgumentException('Telegram user payload is missing.');
        }

        $user = json_decode((string) $payload['user'], true, 512, JSON_THROW_ON_ERROR);

        if (!is_array($user) || !isset($user['id'])) {
            throw new \InvalidArgumentException('Telegram user payload is invalid.');
        }

        return [
            'payload' => $payload,
            'user' => $user,
        ];
    }
}
