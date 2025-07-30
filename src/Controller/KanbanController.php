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
    public function kanban(int $id): Response
    {
        $project = $this->projectRepository->find($id);
        if (!$project) {
            throw $this->createNotFoundException('Projet non trouvé');
        }

        $tasks = $this->taskRepository->findBy(['project' => $project]);

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
    public function addTask(Request $request, int $id ): Response
    {
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
        $user= $this->userRepository->find($assigne);
        $task->setAssigne($user);
        $currentDate = new \DateTime('now');   
        $dateTimeImmutable = DateTimeImmutable::createFromMutable($currentDate);
        $task->setCreatedAt($dateTimeImmutable);

        $this->entityManager->persist($task);
        $this->entityManager->flush();

        return $this->redirectToRoute('project_kanban', ['id' => $id]);
    }

    /**
     * Mettre à jour le statut d'une tâche via AJAX (drag & drop)
     */
    #[Route('/task/{id}/update-status', name: 'task_update_status', methods: ['POST'])]
    public function updateTaskStatus(int $id, Request $request): Response
    {
        $task = $this->taskRepository->find($id);
        if (!$task) {
            return $this->json(['error' => 'Tâche non trouvée'], 404);
        }

        $data = json_decode($request->getContent(), true);
        $newStatus = $data['status'] ?? null;

        $allowedStatuses = ['à faire', 'en cours', 'bloquée', 'terminée'];
        if (!in_array($newStatus, $allowedStatuses, true)) {
            return $this->json(['error' => 'Statut invalide'], 400);
        }

        $task->setStatut($newStatus);

        // Mise à jour automatique de la progression selon statut
        switch ($newStatus) {
            case 'à faire':
                $task->setProgression(0);
                break;
            case 'en cours':
                if ($task->getProgression() === null || $task->getProgression() === 0) {
                    $task->setProgression(25);
                }
                break;
            case 'terminée':
                $task->setProgression(100);
                break;
            // 'bloquée' garde sa progression actuelle
        }

        $this->entityManager->flush();

        return $this->json([
            'success' => true,
            'task' => [
                'id' => $task->getId(),
                'statut' => $task->getStatut(),
                'progression' => $task->getProgression(),
            ],
        ]);
    }
}
