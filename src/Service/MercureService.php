<?php

namespace App\Service;

use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Serializer\SerializerInterface;

class MercureService
{
    public function __construct(
        private readonly HubInterface $hub,
        private readonly SerializerInterface $serializer,
    ) {
    }

    public function publishNotification(object $notification, int $userId): void
    {
        $payload = $this->serializer->serialize($notification, 'json', [
            'groups' => ['notification:read'],
        ]);

        $update = new Update(
            topics: sprintf('/users/%d/notifications', $userId),
            data: $payload,
            private: true,
        );

        $this->hub->publish($update);
    }
}
