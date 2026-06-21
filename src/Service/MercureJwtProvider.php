<?php

namespace App\Service;

class MercureJwtProvider
{
    public function __construct(
        private readonly string $secret,
    ) {
    }

    public function createSubscriberToken(int $userId): string
    {
        $header = self::base64urlEncode(json_encode(['alg' => 'HS256', 'typ' => 'JWT']));
        $payload = self::base64urlEncode(json_encode([
            'mercure' => [
                'subscribe' => [sprintf('/users/%d/notifications', $userId)],
            ],
        ]));

        $signature = self::base64urlEncode(
            hash_hmac('sha256', "$header.$payload", $this->secret, true)
        );

        return "$header.$payload.$signature";
    }

    private static function base64urlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}
