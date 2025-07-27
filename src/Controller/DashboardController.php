<?php

namespace App\Controller;

use App\Repository\DepartementRepository;
use App\Repository\ProjectRepository;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
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
    public function index(Security $security, ProjectRepository $projectRepository, DepartementRepository $departementRepository, TaskRepository $taskRepository, UserRepository $userRepository): Response
    {
        $user = $security->getUser();
        if (in_array('ROLE_PDG', $user->getRoles())) {
            $projet = $projectRepository->findAll();
            $rpojet_en_cours = $projectRepository->findBy(['statut' => 'en cours']);
            $projet_a_faire = $projectRepository->findBy(['statut' => 'à faire']);
            $projet_bloque = $projectRepository->findBy(['statut' => 'bloqué']);
            $projet_termine = $projectRepository->findBy(['statut' => 'terminé']);
            $tache = $taskRepository->findAll();
            $tache_en_cours = $taskRepository->findBy(['statut' => 'en cours']);
            $tache_a_faire = $taskRepository->findBy(['statut' => 'à faire']);
            $tache_bloque = $taskRepository->findBy(['statut' => 'bloqué']);
            $tache_termine = $taskRepository->findBy(['statut' => 'terminé']);
            
            $utilisateur = $userRepository->findAll();
            $utilisateur_collaborateur = [];
            $utilisateur_chef_bu = [];
            $utilisateur_chef_departement = [];
            foreach ($utilisateur as $util) {
                if (in_array('ROLE_USER', $util->getRoles())) {
                    $utilisateur_collaborateur[] = $util;
                } elseif (in_array('ROLE_BU', $util->getRoles())) {
                    $utilisateur_chef_bu[] = $util;
                }
            }
            foreach ($utilisateur as $key => $value) {
                $departement = $departementRepository->findOneBy(['chef' => $value->getId()]);
                if ($departement) {
                    $utilisateur_chef_departement[] = $value;
                }
            }

            return $this->render('dashboard/index.html.twig', [
                'user' => $user,
                'projet_en_cours' => count($rpojet_en_cours),
                'projet_a_faire' => count($projet_a_faire),
                'projet_bloque' => count($projet_bloque),
                'projet_termine' => count($projet_termine),
                'tache_en_cours' => count($tache_en_cours),
                'tache_a_faire' => count($tache_a_faire),
                'tache_bloque' => count($tache_bloque),
                'tache_termine' => count($tache_termine),
                'utilisateur' => count($utilisateur),
                'utilisateur_collaborateur' => count($utilisateur_collaborateur),
                'utilisateur_chef_departement' => count($utilisateur_chef_departement),
                'utilisateur_chef_bu' => count($utilisateur_chef_bu),
                'nbr_projet'=>count($projet),
                'nbr_tache'=>count($tache),
            ]);
        }
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
