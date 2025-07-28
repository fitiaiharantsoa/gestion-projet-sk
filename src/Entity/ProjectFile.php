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

    // Relation correcte avec l'entité Project
    #[ORM\ManyToOne(inversedBy: 'projectFiles')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Project $projet = null;

    #[ORM\Column(length: 50)]
    private ?string $type = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $dateUpload = null;

    // Champ qui stocke l'URL ou le nom du fichier
    #[ORM\Column(length: 255)]
    private ?string $url = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProjet(): ?Project
    {
        return $this->projet;
    }

    public function setProjet(?Project $projet): static
    {
        $this->projet = $projet;

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
}
