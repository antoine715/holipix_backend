<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\ValidationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ValidationRepository::class)]
#[ApiResource]
class Validation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'validations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Photo $photo = null;

    #[ORM\ManyToOne(inversedBy: 'validations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Admin $admin = null;

    #[ORM\Column(length: 20)]
    private ?string $status = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $validatedAt = null;

    public function getId(): ?int { return $this->id; }

    public function getPhoto(): ?Photo { return $this->photo; }
    public function setPhoto(?Photo $photo): static { $this->photo = $photo; return $this; }

    public function getAdmin(): ?Admin { return $this->admin; }
    public function setAdmin(?Admin $admin): static { $this->admin = $admin; return $this; }

    public function getStatus(): ?string { return $this->status; }
    public function setStatus(string $status): static { $this->status = $status; return $this; }

    public function getValidatedAt(): ?\DateTimeImmutable { return $this->validatedAt; }
    public function setValidatedAt(?\DateTimeImmutable $validatedAt): static { $this->validatedAt = $validatedAt; return $this; }
}
