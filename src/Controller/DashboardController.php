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
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
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
        if (in_array('ROLE_BU', $user->getRoles())){
            $bu = $this->getUser()->getBus();
            $id_bu = [];
            foreach ($bu as $b) {
                $id_bu[] = $b->getId();
            }
            $departement = $departementRepository->findBy(['bu' => $id_bu]);
            $id_dep = [];
            foreach ($departement as $key => $value) {
                $id_dep[] = $value->getId();
            }

            $projet = $projectRepository->findBy(['departement' => $id_dep], ['id' => 'DESC']);
            $projet_en_cours = $projectRepository->findBy(['departement' => $id_dep, 'statut'=> 'en cours'], ['id' => 'DESC']);
            $projet_termine = $projectRepository->findBy(['departement' => $id_dep, 'statut'=> 'terminé'], ['id' => 'DESC']);
            $projet_a_faire = $projectRepository->findBy(['departement' => $id_dep, 'statut'=> 'à faire'], ['id' => 'DESC']);
            $projet_bloque = $projectRepository->findBy(['departement' => $id_dep, 'statut'=> 'bloqué'], ['id' => 'DESC']);


            $membres = $userRepository->findBy(['departement'=> $id_dep], ['id'=> 'DESC']);
            $id_pro = [];
            foreach ($projet as $key => $value) {
                $id_pro[] = $value->getId();
            }
            $task = $taskRepository->findBy(['project'=> $id_pro], ['id' => 'DESC']);
            $task_en_cours = $taskRepository->findBy(['project'=> $id_pro, 'statut'=>'en cours'], ['id' => 'DESC']);
            $task_a_faire = $taskRepository->findBy(['project'=> $id_pro, 'statut'=>'à faire'], ['id' => 'DESC']);
            $task_termine = $taskRepository->findBy(['project'=> $id_pro, 'statut'=>'terminé'], ['id' => 'DESC']);
            $task_bloque = $taskRepository->findBy(['project'=> $id_pro, 'statut'=>'bloqué'], ['id' => 'DESC']);
            return $this->render('dashboard/index.html.twig', [
                'user' => $user,
                'projet' => $projet,
                'departement'=>$departement,
                'task'=>$task,
                'membres'=>$membres,
                'projet_en_cours'=>$projet_en_cours,
                'projet_termine'=>$projet_termine,
                'projet_a_faire'=>$projet_a_faire,
                'projet_bloque'=>$projet_bloque,
                'task_en_cours'=>$task_en_cours,
                'task_a_faire'=>$task_a_faire,
                'task_termine'=>$task_termine,
                'task_bloque'=>$task_bloque,
            ]);
        }
        return $this->render('dashboard/index.html.twig', [
            'user' => $user]);
    }

    #[Route('/switch-theme', name: 'app_switch_theme')]
    public function switchTheme(Request $request, SessionInterface $session): Response
    {
        $theme = $session->get('theme', 'light');
        $session->set('theme', $theme === 'dark' ? 'light' : 'dark');

        return $this->redirect($request->headers->get('referer') ?? $this->generateUrl('app_dashboard'));
    }
}
