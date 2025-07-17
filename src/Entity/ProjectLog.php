<?php

namespace App\Entity;

use App\Repository\ProjectLogRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProjectLogRepository::class)]
class ProjectLog
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $action = null;

    #[ORM\Column]
    private ?\DateTime $performedAt = null;

    #[ORM\ManyToOne(inversedBy: 'projectLogs')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $userRef = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Project $project = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAction(): ?string
    {
        return $this->action;
    }

    public function setAction(string $action): static
    {
        $this->action = $action;

        return $this;
    }

    public function getPerformedAt(): ?\DateTime
    {
        return $this->performedAt;
    }

    public function setPerformedAt(\DateTime $performedAt): static
    {
        $this->performedAt = $performedAt;

        return $this;
    }

    public function getUserRef(): ?User
    {
        return $this->userRef;
    }

    public function setUserRef(?User $userRef): static
    {
        $this->userRef = $userRef;

        return $this;
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
}
