<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function home(): Response
    {
        if ($this->getUser()) {
            // Si connecté, redirige vers dashboard
            return $this->redirectToRoute('app_dashboard');
        }

        // Sinon, redirige vers la page de login
        return $this->redirectToRoute('app_login');
    }
}
