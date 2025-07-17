<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]
    #[IsGranted('ROLE_USER')]
    public function index(Security $security): Response
    {
        $user = $security->getUser();
        return $this->render('dashboard/index.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/switch-theme', name: 'app_switch_theme')]
    public function switchTheme(Request $request, SessionInterface $session): Response
    {
        $theme = $session->get('theme', 'light');
        $session->set('theme', $theme === 'dark' ? 'light' : 'dark');

        // Revenir à la page précédente
        $referer = $request->headers->get('referer');
        return $this->redirect($referer ?? $this->generateUrl('app_dashboard'));
    }
}
