<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\AdminRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AdminRepository::class)]
#[ApiResource]
class Admin
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'admins')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(length: 50)]
    private ?string $role = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\OneToMany(mappedBy: 'admin', targetEntity: Validation::class)]
    private Collection $validations;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->validations = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }
    public function getUser(): ?User { return $this->user; }
    public function setUser(?User $user): static { $this->user = $user; return $this; }

    public function getRole(): ?string { return $this->role; }
    public function setRole(string $role): static { $this->role = $role; return $this; }

    public function getCreatedAt(): ?\DateTimeImmutable { return $this->createdAt; }
    public function setCreatedAt(\DateTimeImmutable $createdAt): static { $this->createdAt = $createdAt; return $this; }

    public function getValidations(): Collection { return $this->validations; }
    public function addValidation(Validation $validation): static
    {
        if (!$this->validations->contains($validation)) {
            $this->validations->add($validation);
            $validation->setAdmin($this);
        }
        return $this;
    }
    public function removeValidation(Validation $validation): static
    {
        if ($this->validations->removeElement($validation)) {
            if ($validation->getAdmin() === $this) $validation->setAdmin(null);
        }
        return $this;
    }
}
