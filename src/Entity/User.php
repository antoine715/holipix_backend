<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity]
#[ORM\Table(name: 'users')]
#[ApiResource(
    normalizationContext: ['groups' => ['user:read']],
    denormalizationContext: ['groups' => ['user:write']]
)]
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

    #[ORM\OneToOne(mappedBy: 'commercant', targetEntity: Commerce::class, cascade: ['persist', 'remove'])]
    private ?Commerce $commerce = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Reservation::class)]
    private Collection $reservations;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Payment::class)]
    private Collection $payments;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Photo::class)]
    private Collection $photos;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Review::class)]
    private Collection $reviews;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Language::class)]
    private Collection $languages;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Admin::class)]
    private Collection $admins;

    public function __construct()
    {
        $this->reservations = new ArrayCollection();
        $this->payments = new ArrayCollection();
        $this->photos = new ArrayCollection();
        $this->reviews = new ArrayCollection();
        $this->languages = new ArrayCollection();
        $this->admins = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }
    public function getEmail(): string { return $this->email; }
    public function setEmail(string $email): self { $this->email = $email; return $this; }

    public function getRoles(): array { return $this->roles; }
    public function setRoles(array $roles): self { $this->roles = $roles; return $this; }

    public function getPassword(): string { return $this->password; }
    public function setPassword(string $password): self { $this->password = $password; return $this; }

    public function isVerified(): bool { return $this->isVerified; }
    public function setIsVerified(bool $isVerified): self { $this->isVerified = $isVerified; return $this; }

    public function getVerificationCode(): ?string { return $this->verificationCode; }
    public function setVerificationCode(?string $code): self { $this->verificationCode = $code; return $this; }

    public function getVerifiedAt(): ?\DateTimeImmutable { return $this->verifiedAt; }
    public function setVerifiedAt(?\DateTimeImmutable $dt): self { $this->verifiedAt = $dt; return $this; }

    public function verify(): self
    {
        $this->isVerified = true;
        $this->verificationCode = null;
        $this->verifiedAt = new \DateTimeImmutable();
        return $this;
    }

    public function eraseCredentials() {}
    public function getUserIdentifier(): string { return $this->email; }

    public function getCommerce(): ?Commerce { return $this->commerce; }
    public function setCommerce(?Commerce $commerce): self
    {
        $this->commerce = $commerce;
        return $this;
    }
}
