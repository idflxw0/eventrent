<?php

namespace App\EventSubscriber;

use App\Entity\Notification;
use App\Service\MercureService;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Events;

#[AsEntityListener(event: Events::postPersist, entity: Notification::class)]
class NotificationSubscriber
{
    public function __construct(
        private readonly MercureService $mercureService,
    ) {
    }

    public function postPersist(Notification $notification, PostPersistEventArgs $args): void
    {
        $this->mercureService->publishNotification(
            $notification,
            $notification->getUser()->getId()
        );
    }
}
