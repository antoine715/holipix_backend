<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\SearchRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SearchRepository::class)]
#[ApiResource]
class Search
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $city = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $type = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTime $dateForm = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTime $dateTo = null;

    #[ORM\Column(nullable: true)]
    private ?int $adults = null;

    #[ORM\Column(nullable: true)]
    private ?int $children = null;

    #[ORM\Column(nullable: true)]
    private ?int $rooms = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getDateForm(): ?\DateTime
    {
        return $this->dateForm;
    }

    public function setDateForm(?\DateTime $dateForm): static
    {
        $this->dateForm = $dateForm;

        return $this;
    }

    public function getDateTo(): ?\DateTime
    {
        return $this->dateTo;
    }

    public function setDateTo(?\DateTime $dateTo): static
    {
        $this->dateTo = $dateTo;

        return $this;
    }

    public function getAdults(): ?int
    {
        return $this->adults;
    }

    public function setAdults(?int $adults): static
    {
        $this->adults = $adults;

        return $this;
    }

    public function getChildren(): ?int
    {
        return $this->children;
    }

    public function setChildren(?int $children): static
    {
        $this->children = $children;

        return $this;
    }

    public function getRooms(): ?int
    {
        return $this->rooms;
    }

    public function setRooms(?int $rooms): static
    {
        $this->rooms = $rooms;

        return $this;
    }
}
