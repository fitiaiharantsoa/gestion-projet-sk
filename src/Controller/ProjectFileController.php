<?php

namespace App\Controller;

use App\Entity\ProjectFile;
use App\Form\ProjectFileType;
use App\Repository\ProjectFileRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/project-file')]
final class ProjectFileController extends AbstractController
{
    #[Route('/', name: 'app_project_file_index', methods: ['GET'])]
    public function index(ProjectFileRepository $projectFileRepository): Response
    {
        return $this->render('project_file/index.html.twig', [
            'project_files' => $projectFileRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_project_file_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $projectFile = new ProjectFile();
        $form = $this->createForm(ProjectFileType::class, $projectFile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gestion du fichier uploadé
            $uploadedFile = $form->get('fichier')->getData();

            if ($uploadedFile) {
                $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
                // Nettoyage du nom de fichier pour éviter les caractères problématiques
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$uploadedFile->guessExtension();

                // Déplacement du fichier dans le dossier "public/uploads/project_files"
                try {
                    $uploadedFile->move(
                        $this->getParameter('project_files_directory'), // définir ce paramètre dans config/services.yaml
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors du téléchargement du fichier.');
                    // Tu peux logger ou gérer autrement l'exception
                }

                // Mise à jour de l'entité avec le nom du fichier (url)
                $projectFile->setUrl($newFilename);
            }

            // Persist et flush
            $entityManager->persist($projectFile);
            $entityManager->flush();

            $this->addFlash('success', 'Fichier ajouté avec succès.');

            return $this->redirectToRoute('app_project_file_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('project_file/file.html.twig', [
            'project_file' => $projectFile,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/show/{id}', name: 'app_project_file_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(ProjectFile $projectFile): Response
    {
        return $this->render('project_file/show.html.twig', [
            'project_file' => $projectFile,
        ]);
    }

    #[Route('/edit/{id}', name: 'app_project_file_edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function edit(Request $request, ProjectFile $projectFile, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(ProjectFileType::class, $projectFile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Même gestion du fichier uploadé si tu souhaites autoriser la modification du fichier
            $dateUploadFile = $form->get('fichier')->getData();

            if ($dateUploadFile) {
                $originalFilename = pathinfo($dateUploadFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$dateUploadFile->guessExtension();

                try {
                    $dateUploadFile->move(
                        $this->getParameter('project_files_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors du téléchargement du fichier.');
                }

                $projectFile->setUrl($newFilename);
            }

            $entityManager->flush();

            $this->addFlash('success', 'Fichier modifié avec succès.');

            return $this->redirectToRoute('app_project_file_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('project_file/edit.html.twig', [
            'project_file' => $projectFile,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/delete/{id}', name: 'app_project_file_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(Request $request, ProjectFile $projectFile, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$projectFile->getId(), $request->request->get('_token'))) {
            $entityManager->remove($projectFile);
            $entityManager->flush();
            $this->addFlash('success', 'Fichier supprimé avec succès.');
        }

        return $this->redirectToRoute('app_project_file_index', [], Response::HTTP_SEE_OTHER);
    }
}
