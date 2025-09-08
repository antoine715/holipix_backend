<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\PhotoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PhotoRepository::class)]
#[ApiResource]
class Photo
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $url = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'photos')]
    #[ORM\JoinColumn(nullable: true, onDelete: "CASCADE")]
    private ?Commerce $commerce = null;

    #[ORM\ManyToOne(inversedBy: 'photos')]
    #[ORM\JoinColumn(nullable: true, onDelete: "CASCADE")]
    private ?User $user = null;

    #[ORM\OneToMany(mappedBy: 'photo', targetEntity: Validation::class)]
    private Collection $validations;

    public function __construct() { $this->validations = new ArrayCollection(); }

    public function getId(): ?int { return $this->id; }
    public function getUrl(): ?string { return $this->url; }
    public function setUrl(string $url): static { $this->url = $url; return $this; }
    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): static { $this->description = $description; return $this; }
    public function getCommerce(): ?Commerce { return $this->commerce; }
    public function setCommerce(?Commerce $commerce): static { $this->commerce = $commerce; return $this; }
    public function getUser(): ?User { return $this->user; }
    public function setUser(?User $user): static { $this->user = $user; return $this; }
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
