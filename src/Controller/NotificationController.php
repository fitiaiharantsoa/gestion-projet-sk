<?php

namespace App\Controller;

use App\Entity\Notification;
use App\Form\NotificationType;
use App\Repository\NotificationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\SecurityBundle\Security;

#[Route('/notification')]
final class NotificationController extends AbstractController
{
    #[Route(name: 'app_notification_index', methods: ['GET'])]
    public function index(NotificationRepository $notificationRepository): Response
    {
        return $this->render('notification/index.html.twig', [
            'notifications' => $notificationRepository->findBy(["recipient"=> $this->getUser()]),
        ]);
    }

    #[Route('/mes', name: 'app_notification_mine', methods: ['GET'])]
    public function myNotifications(NotificationRepository $notificationRepository): Response
    {
        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour voir vos notifications.');
        }

        $notifications = $notificationRepository->findBy(
            ['recipient' => $user],
            ['createdAt' => 'DESC']
        );

        return $this->render('notification/mine.html.twig', [
            'notifications' => $notifications,
        ]);
    }

    #[Route('/new', name: 'app_notification_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, Security $security): Response
    {
        $notification = new Notification();

        $notification->setCreatedAt(new \DateTimeImmutable());

       
        $notification->setCreatedBy($security->getUser());

        $form = $this->createForm(NotificationType::class, $notification);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($notification);
            $entityManager->flush();

            return $this->redirectToRoute('app_notification_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('notification/new.html.twig', [
            'notification' => $notification,
            'form' => $form,
        ]);
    }


    #[Route('/{id}', name: 'app_notification_show', methods: ['GET'])]
    public function show(Notification $notification , EntityManagerInterface $entityManager ): Response
    {
        $notification->setSeen(true); // Marquer la notification comme lue
        $entityManager->flush();
        return $this->render('notification/show.html.twig', [
            'notification' => $notification,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_notification_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Notification $notification, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(NotificationType::class, $notification);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_notification_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('notification/edit.html.twig', [
            'notification' => $notification,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_notification_delete', methods: ['POST'])]
    public function delete(Request $request, Notification $notification, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$notification->getId(), $request->request->get('_token'))) {
            $entityManager->remove($notification);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_notification_index', [], Response::HTTP_SEE_OTHER);
    }

}


