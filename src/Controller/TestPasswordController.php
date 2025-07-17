<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class TestPasswordController extends AbstractController
{
    #[Route('/reset-test-pass', name: 'app_test_password')]
    public function resetPassword(
        UserRepository $userRepo,
        UserPasswordHasherInterface $hasher,
        EntityManagerInterface $em
    ): Response {
        $user = $userRepo->findOneBy(['email' => 'ft.iharantsoa@gmail.com']);
        if (!$user) {
            return new Response('Utilisateur introuvable ❌');
        }

        $newPassword = 'fitia123';
        $user->setPassword($hasher->hashPassword($user, $newPassword));
        $em->flush();

        return new Response('Mot de passe réinitialisé à : fitia123 ✅');
    }
}
