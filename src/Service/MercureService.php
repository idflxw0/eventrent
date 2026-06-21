<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class MercureService
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly LoggerInterface $logger,
        private readonly HttpClientInterface $httpClient,
        private readonly string $mercureHubUrl,
        private readonly string $mercureJwtSecret,
    ) {
    }

    public function publishNotification(object $notification, int $userId): void
    {
        try {
            $payload = $this->serializer->serialize($notification, 'json', [
                'groups' => ['notification:read'],
            ]);

            $jwt = $this->createPublisherJwt();

            $response = $this->httpClient->request('POST', $this->mercureHubUrl, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $jwt,
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
                'body' => http_build_query([
                    'topic' => sprintf('/users/%d/notifications', $userId),
                    'data' => $payload,
                    'private' => 'on',
                ]),
            ]);

            // Force immediate send — HttpClient is async by default
            $response->getStatusCode();
        } catch (\Throwable $e) {
            $this->logger->error('Mercure publish failed: ' . $e->getMessage());
        }
    }

    private function createPublisherJwt(): string
    {
        $header = rtrim(strtr(base64_encode(json_encode(['alg' => 'HS256'])), '+/', '-_'), '=');
        $payload = rtrim(strtr(base64_encode(json_encode(['mercure' => ['publish' => ['*']]])), '+/', '-_'), '=');
        $sig = rtrim(strtr(base64_encode(hash_hmac('sha256', "$header.$payload", $this->mercureJwtSecret, true)), '+/', '-_'), '=');

        return "$header.$payload.$sig";
    }
}
