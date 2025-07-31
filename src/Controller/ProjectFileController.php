<?php

namespace App\Controller;

use App\Entity\ProjectFile;
use App\Form\ProjectFileType;
use App\Repository\ProjectFileRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
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
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger, #[Autowire('%kernel.project_dir%/public/uploads/file')]string $projectDir): Response
    {
        $projectFile = new ProjectFile();
        $form = $this->createForm(ProjectFileType::class, $projectFile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $uploadFile = $form->get('project_file')->getData();
            if ($uploadFile) {
                if ($uploadFile){
                    $originalName = pathinfo($uploadFile->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalName);
                    $newFilename = $safeFilename.'-'.uniqid().'.'.$uploadFile->guessExtension();
    
                    try {
                        $uploadFile->move(
                            $projectDir,
                            $newFilename
                        );
                    } catch (\Throwable $th) {
                        throw $th;
                    }
                    $projectFile->setFilepath($newFilename);
                    $projectFile->setDateUpload(new \DateTimeImmutable('now'));
                }
            }

            $entityManager->persist($projectFile);
            $entityManager->flush();

            return $this->redirectToRoute('app_project_file_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('project_file/file.html.twig', [
            'project_file' => $projectFile,
            'form' => $form,
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
    public function edit(Request $request, ProjectFile $projectFile, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProjectFileType::class, $projectFile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_project_file_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('project_file/edit.html.twig', [
            'project_file' => $projectFile,
            'form' => $form,
        ]);
    }

    #[Route('/delete/{id}', name: 'app_project_file_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(Request $request, ProjectFile $projectFile, EntityManagerInterface $entityManager, #[Autowire('%kernel.project_dir%/public/uploads/file')]string $projectDir): Response
    {
        if ($this->isCsrfTokenValid('delete'.$projectFile->getId(), $request->request->get('_token'))) {
            if (file_exists($projectDir.'/'.$projectFile->getFilepath())) {
                unlink($projectDir.'/'.$projectFile->getFilepath());
            }
            $entityManager->remove($projectFile);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_project_file_index', [], Response::HTTP_SEE_OTHER);
    }
}
