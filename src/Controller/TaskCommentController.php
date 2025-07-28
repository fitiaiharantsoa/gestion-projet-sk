<?php

namespace App\Controller;

use App\Entity\Notification;
use App\Entity\TaskComment;
use App\Form\TaskCommentType;
use App\Repository\TaskCommentRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/task/comment')]
final class TaskCommentController extends AbstractController
{
    #[Route(name: 'app_task_comment_index', methods: ['GET'])]
    public function index(TaskCommentRepository $taskCommentRepository): Response
    {
        return $this->render('task_comment/index.html.twig', [
            'task_comments' => $taskCommentRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_task_comment_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, UserRepository $userRepository): Response
    {
        $taskComment = new TaskComment();
        $form = $this->createForm(TaskCommentType::class, $taskComment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Vérifier que l'utilisateur est connecté
            $currentUser = $this->getUser();
            if (!$currentUser) {
                throw $this->createAccessDeniedException('Vous devez être connecté pour créer un commentaire.');
            }

            // Enregistrer le commentaire
            $entityManager->persist($taskComment);
            $entityManager->flush();

            // Extraire les mentions @email dans le message (format simple email)
            preg_match_all('/@([\w.\-]+@[\w\-]+\.[\w.]+)/', $taskComment->getMessage(), $matches);
            $emails = $matches[1] ?? [];

            foreach ($emails as $email) {
                $user = $userRepository->findOneBy(['email' => $email]);
                if ($user) {
                    $notification = new Notification();
                    $notification->setCreatedBy($currentUser); // Auteur du commentaire
                    $notification->setCreatedAt(new \DateTimeImmutable());
                    $notification->setMessage('Vous avez été mentionné dans un commentaire d’une tâche.');
                    $notification->setSeen(false);
                    $notification->setRecipient($user); // Destinataire

                    $entityManager->persist($notification);

                    // Debug : afficher dans la barre debug Symfony ou console
                    dump('Notification créée pour : ' . $user->getEmail());
                } else {
                    dump('Aucun utilisateur trouvé pour : ' . $email);
                }
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_task_comment_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('task_comment/new.html.twig', [
            'task_comment' => $taskComment,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_task_comment_show', methods: ['GET'])]
    public function show(TaskComment $taskComment): Response
    {
        return $this->render('task_comment/show.html.twig', [
            'task_comment' => $taskComment,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_task_comment_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, TaskComment $taskComment, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TaskCommentType::class, $taskComment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_task_comment_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('task_comment/edit.html.twig', [
            'task_comment' => $taskComment,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_task_comment_delete', methods: ['POST'])]
    public function delete(Request $request, TaskComment $taskComment, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$taskComment->getId(), $request->request->get('_token'))) {
            $entityManager->remove($taskComment);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_task_comment_index', [], Response::HTTP_SEE_OTHER);
    }
}
