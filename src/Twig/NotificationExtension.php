<?php

namespace App\Twig;

use App\Repository\NotificationRepository;
use Symfony\Bundle\SecurityBundle\Security; // <-- Bien vérifier ce use
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

class NotificationExtension extends AbstractExtension implements GlobalsInterface
{
    private NotificationRepository $notificationRepository;
    private Security $security;

    public function __construct(NotificationRepository $notificationRepository, Security $security)
    {
        $this->notificationRepository = $notificationRepository;
        $this->security = $security;
    }

    public function getGlobals(): array
    {
        $user = $this->security->getUser();

        if (!$user) {
            return [
                'notifications_unread' => [],
                'notifications_unread_count' => 0,
            ];
        }

        $notifications = $this->notificationRepository->findUnreadByRecipient($user);
        $count = count($notifications);

        return [
            'notifications_unread' => $notifications,
            'notifications_unread_count' => $count,
        ];
    }
}
