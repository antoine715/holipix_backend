<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity]
#[ORM\Table(name: 'users')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    private string $email;

    #[ORM\Column(type: 'json')]
    private array $roles = [];

    #[ORM\Column(type: 'string')]
    private string $password;

    #[ORM\Column(type: 'boolean')]
    private bool $isVerified = false;

    #[ORM\Column(type: 'string', length: 6, nullable: true)]
    private ?string $verificationCode = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $verifiedAt = null;

    // -----------------------------
    // Getters / Setters
    // -----------------------------

    public function getId(): ?int 
    { 
        return $this->id; 
    }

    public function getEmail(): string 
    { 
        return $this->email; 
    }

    public function setEmail(string $email): self 
    { 
        $this->email = $email; 
        return $this; 
    }

    public function getRoles(): array 
    { 
        return $this->roles; 
    }

    public function setRoles(array $roles): self 
    { 
        $this->roles = $roles; 
        return $this; 
    }

    public function getPassword(): string 
    { 
        return $this->password; 
    }

    public function setPassword(string $password): self 
    { 
        $this->password = $password; 
        return $this; 
    }

    public function getIsVerified(): bool 
    { 
        return $this->isVerified; 
    }

    public function setIsVerified(bool $isVerified): self 
    { 
        $this->isVerified = $isVerified; 
        return $this; 
    }

    public function getVerificationCode(): ?string 
    { 
        return $this->verificationCode; 
    }

    public function setVerificationCode(?string $code): self 
    { 
        $this->verificationCode = $code; 
        return $this; 
    }

    public function getVerifiedAt(): ?\DateTimeImmutable
    {
        return $this->verifiedAt;
    }

    public function setVerifiedAt(?\DateTimeImmutable $verifiedAt): self
    {
        $this->verifiedAt = $verifiedAt;
        return $this;
    }

    // -----------------------------
    // Helpers / Verification
    // -----------------------------

    // Pour pouvoir appeler $user->isVerified()
    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    // Confirme l'utilisateur
    public function verify(): self
    {
        $this->isVerified = true;
        $this->verificationCode = null;
        $this->verifiedAt = new \DateTimeImmutable();
        return $this;
    }

    // -----------------------------
    // UserInterface / Security
    // -----------------------------

    public function eraseCredentials() {}

    public function getUserIdentifier(): string 
    { 
        return $this->email; 
    }
}
