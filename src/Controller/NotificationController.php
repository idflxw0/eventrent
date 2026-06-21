<?php

namespace App\Controller;

use App\Entity\Notification;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class NotificationController extends AbstractController
{
    #[Route('/notifications', name: 'notification_index')]
    public function index(): Response
    {
        $notifications = $this->getUser()->getNotifications();

        return $this->render('notification/index.html.twig', [
            'notifications' => $notifications,
        ]);
    }

    #[Route('/api/notifications/unread-count', name: 'api_notification_unread_count', methods: ['GET'])]
    public function unreadCount(): JsonResponse
    {
        $count = $this->getUser()->getNotifications()
            ->filter(fn(Notification $n) => !$n->isRead())
            ->count();

        return new JsonResponse(['count' => $count]);
    }

    #[Route('/notifications/{id}/read', name: 'notification_read', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function markRead(int $id, EntityManagerInterface $em): Response
    {
        $notification = $em->getRepository(Notification::class)->find($id);

        if (!$notification || $notification->getUser() !== $this->getUser()) {
            throw $this->createNotFoundException();
        }

        $notification->setRead(true);
        $em->flush();

        return $this->redirectToRoute('notification_index');
    }
}
