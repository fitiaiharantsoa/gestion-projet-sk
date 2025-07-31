<?php

namespace App\Entity;

use App\Repository\ProjectFileRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProjectFileRepository::class)]
class ProjectFile
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // Relation avec l'entité Project - NULLABLE pour permettre upload sans projet
    #[ORM\ManyToOne(inversedBy: 'projectFiles')]
    #[ORM\JoinColumn(nullable: true)]  // Changé de false à true
    private ?Project $project = null;

    // Type MIME du fichier
    #[ORM\Column(length: 100)]
    private ?string $type = null;

    // Date d'upload (gardons celui-ci)
    #[ORM\Column]
    private ?\DateTimeImmutable $dateUpload = null;

    // Nom du fichier stocké sur le serveur
    #[ORM\Column(length: 255)]
    private ?string $url = null;

    // Nom original du fichier
    #[ORM\Column(length: 255)]
    private ?string $filename = null;

    // SUPPRIMÉ: uploadedAt car redondant avec dateUpload
    // Si vous voulez le garder, supprimez dateUpload à la place

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(?Project $project): static
    {
        $this->project = $project;
        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;
        return $this;
    }

    public function getDateUpload(): ?\DateTimeImmutable
    {
        return $this->dateUpload;
    }

    public function setDateUpload(\DateTimeImmutable $dateUpload): static
    {
        $this->dateUpload = $dateUpload;
        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): static
    {
        $this->url = $url;
        return $this;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): static
    {
        $this->filename = $filename;
        return $this;
    }
}