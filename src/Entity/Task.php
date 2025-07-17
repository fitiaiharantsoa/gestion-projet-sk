<?php

namespace App\Entity;

use App\Repository\TaskRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TaskRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Task
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 200)]
    private string $titre;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    /**
     * Statut possible : 'à faire', 'en cours', 'bloquée', 'terminée'
     */
    #[ORM\Column(length: 50)]
    private string $statut = 'à faire';

    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $dateEcheance = null;

    /**
     * Priorité : 'haute', 'moyenne', 'basse' ou null
     */
    #[ORM\Column(length: 20, nullable: true)]
    private ?string $priorite = null;

    /**
     * Progression en pourcentage (0 à 100)
     */
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $progression = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private ?User $assigne = null;

    #[ORM\ManyToOne(targetEntity: Project::class, inversedBy: 'tasks')]
    private ?Project $project = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $updatedAt = null;

    public function __construct()
    {
        // Initialise la progression à 0 par défaut si null
        if ($this->progression === null) {
            $this->progression = 0;
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getStatut(): string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): self
    {
        $this->statut = $statut;
        return $this;
    }

    public function getDateEcheance(): ?\DateTimeInterface
    {
        return $this->dateEcheance;
    }

    public function setDateEcheance(?\DateTimeInterface $dateEcheance): self
    {
        $this->dateEcheance = $dateEcheance;
        return $this;
    }

    public function getPriorite(): ?string
    {
        return $this->priorite;
    }

    public function setPriorite(?string $priorite): self
    {
        $this->priorite = $priorite;
        return $this;
    }

    public function getProgression(): ?int
    {
        return $this->progression;
    }

    public function setProgression(?int $progression): self
    {
        $this->progression = $progression;
        return $this;
    }

    public function getAssigne(): ?User
    {
        return $this->assigne;
    }

    public function setAssigne(?User $assigne): self
    {
        $this->assigne = $assigne;
        return $this;
    }

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(?Project $project): self
    {
        $this->project = $project;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        $now = new \DateTime();
        $this->createdAt = $now;
        $this->updatedAt = $now;
        $this->updateStatutFromProgression();
    }

    #[ORM\PreUpdate]
    public function onPreUpdate(): void
    {
        $this->updatedAt = new \DateTime();
        $this->updateStatutFromProgression();
    }

    private function updateStatutFromProgression(): void
    {
        if ($this->statut !== 'bloquée') { // ne pas écraser si bloquée
            if ($this->progression === 100) {
                $this->statut = 'terminée';
            } elseif ($this->progression > 0) {
                $this->statut = 'en cours';
            } elseif ($this->progression === 0 || $this->progression === null) {
                $this->statut = 'à faire';
            }
        }
    }
}
