<?php

namespace App\Entity;

use App\Repository\UserTrustedDeviceRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserTrustedDeviceRepository::class)]
class UserTrustedDevice
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $owner = null;

    #[ORM\Column(length: 255)]
    private ?string $deviceToken = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $expiresA = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $userAgent = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): static
    {
        $this->owner = $owner;

        return $this;
    }

    public function getDeviceToken(): ?string
    {
        return $this->deviceToken;
    }

    public function setDeviceToken(string $deviceToken): static
    {
        $this->deviceToken = $deviceToken;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getExpiresA(): ?\DateTimeImmutable
    {
        return $this->expiresA;
    }

    public function setExpiresA(\DateTimeImmutable $expiresA): static
    {
        $this->expiresA = $expiresA;

        return $this;
    }

    public function getUserAgent(): ?string
    {
        return $this->userAgent;
    }

    public function setUserAgent(?string $userAgent): static
    {
        $this->userAgent = $userAgent;

        return $this;
    }
}
