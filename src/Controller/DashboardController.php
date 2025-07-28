<?php

namespace App\Controller;

use App\Repository\NotificationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]
    #[IsGranted('ROLE_USER')]
    public function index(Security $security, NotificationRepository $notificationRepository): Response
    {
        $user = $security->getUser();

        // Nombre de notifications non lues
        $unreadCount = $notificationRepository->countUnreadForUser($user);

        // Liste des notifications non lues
        $unreadNotifications = $notificationRepository->findUnreadByRecipient($user);

        return $this->render('dashboard/index.html.twig', [
            'user' => $user,
            'unreadCount' => $unreadCount,
            'unreadNotifications' => $unreadNotifications,
        ]);
    }

    #[Route('/switch-theme', name: 'app_switch_theme')]
    public function switchTheme(Request $request, SessionInterface $session): Response
    {
        $theme = $session->get('theme', 'light');
        $session->set('theme', $theme === 'dark' ? 'light' : 'dark');

        return $this->redirect($request->headers->get('referer') ?? $this->generateUrl('app_dashboard'));
    }
}
