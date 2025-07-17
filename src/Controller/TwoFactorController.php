<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TwoFactorController extends AbstractController
{
    #[Route('/2fa', name: 'app_2fa_login')]
    public function form(): Response
    {
        return $this->render('security/2fa_login.html.twig');
    }

    #[Route('/2fa_check', name: 'app_2fa_login_check')]
    public function check(): void
    {
        // Cette méthode ne doit pas être appelée directement
        throw new \Exception('Ne devrait pas être appelée directement.');
    }
}
