<?php

namespace App\Twig;

use App\Entity\User;
use App\Repository\NotificationRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class NotificationExtension extends AbstractExtension
{
    public function __construct(private readonly NotificationRepository $notificationRepository) {}

    public function getFunctions(): array
    {
        return [
            new TwigFunction('unread_notification_count', $this->countUnread(...)),
        ];
    }

    public function countUnread(User $user): int
    {
        return $this->notificationRepository->countUnreadForUser($user);
    }
}
