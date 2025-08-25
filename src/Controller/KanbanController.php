<?php

namespace App\Controller;

use App\Entity\Task;
use App\Entity\User;
use DateTimeImmutable;
use App\Repository\TaskRepository;
use App\Repository\ProjectRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class KanbanController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private TaskRepository $taskRepository;
    private ProjectRepository $projectRepository;
    private UserRepository $userRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        TaskRepository $taskRepository,
        ProjectRepository $projectRepository,
        UserRepository $userRepository
    ) {
        $this->entityManager = $entityManager;
        $this->taskRepository = $taskRepository;
        $this->projectRepository = $projectRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * Afficher la vue Kanban d'un projet
     */
    #[Route('/project/{id}/kanban', name: 'project_kanban')]
    public function index(int $id): Response
    {
        $project = $this->projectRepository->find($id);
        if (!$project) {
            throw $this->createNotFoundException('Projet non trouvé');
        }

        $currentUser = $this->getUser();
        
        // Déterminer les tâches visibles selon le rôle de l'utilisateur
        if ($this->isGranted('ROLE_PDG') || $this->isGranted('ROLE_CHEF_DEPARTEMENT')) {
            // PDG et Chef de département voient TOUTES les tâches du projet
            $tasks = $this->taskRepository->findBy(['project' => $project]);
        } else {
            // Collaborateur normal voit SEULEMENT ses propres tâches
            $tasks = $this->taskRepository->findBy([
                'project' => $project,
                'assigne' => $currentUser
            ]);
        }

        // Organiser les tâches par statut (les clés correspondent aux valeurs exactes en base)
        $tasksByStatus = [
            'à faire' => [],
            'en cours' => [],
            'bloquée' => [],
            'terminée' => [],
        ];

        foreach ($tasks as $task) {
            $statut = $task->getStatut();
            if (array_key_exists($statut, $tasksByStatus)) {
                $tasksByStatus[$statut][] = $task;
            }
        }

        return $this->render('kanban/index.html.twig', [
            'project' => $project,
            'tasksByStatus' => $tasksByStatus,
            'allTasks' => $tasks,
            'users'=> $this->userRepository->findAll(),
        ]);
    }

    /**
     * Ajouter une nouvelle tâche via formulaire POST (dans modal)
     */
    #[Route('/project/{id}/kanban/add-task', name: 'kanban_add_task', methods: ['POST'])]
    public function addTask(Request $request, int $id): Response
    {
        // Seuls PDG et Chef de département peuvent créer des tâches
        if (!$this->isGranted('ROLE_PDG') && !$this->isGranted('ROLE_CHEF_DEPARTEMENT')) {
            throw $this->createAccessDeniedException('Seuls le PDG et les chefs de département peuvent créer des tâches.');
        }

        $project = $this->projectRepository->find($id);
        if (!$project) {
            throw $this->createNotFoundException('Projet non trouvé');
        }

        $title = $request->request->get('title');
        $description = $request->request->get('description');
        $priority = $request->request->get('priority');
        $dueDate = $request->request->get('due_date');
        $assigne = $request->request->get('assigne');

        $task = new Task();
        $task->setTitre($title);
        $task->setDescription($description);
        $task->setPriorite($priority);
        $task->setDateEcheance($dueDate ? new \DateTime($dueDate) : null);
        $task->setStatut('à faire'); // statut par défaut
        $task->setProject($project);
        $user = $this->userRepository->find($assigne);
        $task->setAssigne($user);
        $currentDate = new \DateTime('now');   
        $dateTimeImmutable = DateTimeImmutable::createFromMutable($currentDate);
        $task->setCreatedAt($dateTimeImmutable);
        $task->setCreateur($this->getUser()->getId());
        $this->entityManager->persist($task);
        $this->entityManager->flush();

        return $this->redirectToRoute('project_kanban', ['id' => $id]);
    }
}