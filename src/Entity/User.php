<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Scheb\TwoFactorBundle\Model\Email\TwoFactorInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface, TwoFactorInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    /**
     * @var list<string>
     */
    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column]
    private bool $isVerified = false;

    #[ORM\Column]
    private bool $isEmailAuthEnabled = false;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $authCode = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nom = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $prenom = null;

    /**
     * @var Collection<int, ProjectLog>
     */
    #[ORM\OneToMany(targetEntity: ProjectLog::class, mappedBy: 'user')]
    private Collection $projectLogs;

    /**
     * @var Collection<int, Departement>
     */
    #[ORM\OneToMany(targetEntity: Departement::class, mappedBy: 'chef')]
    private Collection $departements;

    #[ORM\ManyToOne(inversedBy: 'users')]
    private ?Departement $departement = null;

    public function __construct()
    {
        $this->projectLogs = new ArrayCollection();
        $this->departements = new ArrayCollection();
    }

    // --- Getters and setters ---

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): static
    {
        $this->nom = $nom;
        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(?string $prenom): static
    {
        $this->prenom = $prenom;
        return $this;
    }



    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;

        if (empty($roles)) {
            return ['ROLE_USER'];
        }

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    public function eraseCredentials(): void
    {
        // Clear temporary sensitive data here if any
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;
        return $this;
    }

    /**
     * @return Collection<int, ProjectLog>
     */
    public function getProjectLogs(): Collection
    {
        return $this->projectLogs;
    }

    public function addProjectLog(ProjectLog $projectLog): static
    {
        if (!$this->projectLogs->contains($projectLog)) {
            $this->projectLogs->add($projectLog);
            $projectLog->setUser($this);
        }
        return $this;
    }

    public function removeProjectLog(ProjectLog $projectLog): static
    {
        if ($this->projectLogs->removeElement($projectLog)) {
            if ($projectLog->getUser() === $this) {
                $projectLog->setUser(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, Departement>
     */
    public function getDepartements(): Collection
    {
        return $this->departements;
    }

    public function addDepartement(Departement $departement): static
    {
        if (!$this->departements->contains($departement)) {
            $this->departements->add($departement);
            $departement->setChef($this);
        }
        return $this;
    }

    public function removeDepartement(Departement $departement): static
    {
        if ($this->departements->removeElement($departement)) {
            if ($departement->getChef() === $this) {
                $departement->setChef(null);
            }
        }
        return $this;
    }

    // TwoFactorInterface methods...

    public function getEmailAuthRecipient(): string
    {
        return $this->email;
    }

    public function isEmailAuthEnabled(): bool
{
    return $this->isVerified && $this->isEmailAuthEnabled;
}


    public function setIsEmailAuthEnabled(bool $isEnabled): static
    {
        $this->isEmailAuthEnabled = $isEnabled;
        return $this;
    }

    public function getEmailAuthCode(): string
    {
        if ($this->authCode === null) {
            throw new \LogicException('Le code d\'authentification email n\'est pas défini.');
        }
        return $this->authCode;
    }

    public function setEmailAuthCode(string $authCode): void
    {
        $this->authCode = $authCode;
    }

    // Useful to display full name

    public function getFullName(): string
    {
        $parts = array_filter([$this->prenom, $this->nom]);
        return empty($parts) ? $this->email : implode(' ', $parts);
    }

    public function __toString(): string
    {
        return $this->getFullName();
    }

    public function getDepartement(): ?Departement
    {
        return $this->departement;
    }

    public function setDepartement(?Departement $departement): static
    {
        $this->departement = $departement;

        return $this;
    }
}
