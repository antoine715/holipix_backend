<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\PhotoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: PhotoRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['photo:read']],
    denormalizationContext: ['groups' => ['photo:write']]
)]
class Photo
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['photo:read', 'commerce:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['photo:read', 'photo:write', 'commerce:read'])]
    private ?string $url = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['photo:read', 'photo:write', 'commerce:read'])]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'photos')]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    #[Groups(['photo:read', 'photo:write'])]
    private ?Commerce $commerce = null;

    #[ORM\ManyToOne(inversedBy: 'photos')]
    #[ORM\JoinColumn(nullable: true, onDelete: "SET NULL")]
    #[Groups(['photo:read'])]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'photos')]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    #[Groups(['photo:read', 'photo:write'])]
    private ?Reservation $reservation = null;

    #[ORM\Column(type: 'boolean')]
    #[Groups(['photo:read', 'photo:write'])]
    private bool $validated = false;

    #[ORM\OneToMany(mappedBy: 'photo', targetEntity: Validation::class, cascade: ['persist', 'remove'])]
    private Collection $validations;

    public function __construct()
    {
        $this->validations = new ArrayCollection();
    }

    // ────────────── Getters / Setters ──────────────

    public function getId(): ?int { return $this->id; }
    public function getUrl(): ?string { return $this->url; }
    public function setUrl(string $url): static { $this->url = $url; return $this; }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): static { $this->description = $description; return $this; }

    public function getCommerce(): ?Commerce { return $this->commerce; }
    public function setCommerce(?Commerce $commerce): static { $this->commerce = $commerce; return $this; }

    public function getUser(): ?User { return $this->user; }
    public function setUser(?User $user): static { $this->user = $user; return $this; }

    public function getReservation(): ?Reservation { return $this->reservation; }
    public function setReservation(?Reservation $reservation): static { $this->reservation = $reservation; return $this; }

    public function isValidated(): bool { return $this->validated; }
    public function setValidated(bool $validated): static { $this->validated = $validated; return $this; }

    public function getValidations(): Collection { return $this->validations; }
    public function addValidation(Validation $validation): static
    {
        if (!$this->validations->contains($validation)) {
            $this->validations->add($validation);
            $validation->setPhoto($this);
        }
        return $this;
    }

    public function removeValidation(Validation $validation): static
    {
        if ($this->validations->removeElement($validation)) {
            if ($validation->getPhoto() === $this) $validation->setPhoto(null);
        }
        return $this;
    }
}
