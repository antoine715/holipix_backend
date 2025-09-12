<?php

namespace App\Entity;

use App\Repository\OfferRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OfferRepository::class)]
class Offer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type:"integer")]
    private ?int $id = null;

    #[ORM\Column(type:"string", length:255)]
    private ?string $name = null;

    #[ORM\Column(type:"float")]
    private ?float $price = null;

    #[ORM\ManyToOne(targetEntity: Commerce::class, inversedBy: "offers")]
    #[ORM\JoinColumn(nullable: false)]
    private ?Commerce $commerce = null;

    #[ORM\OneToMany(mappedBy: "offer", targetEntity: Reservation::class)]
    private Collection $reservations;

    public function __construct()
    {
        $this->reservations = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }
    public function getName(): ?string { return $this->name; }
    public function setName(string $name): self { $this->name = $name; return $this; }
    public function getPrice(): ?float { return $this->price; }
    public function setPrice(float $price): self { $this->price = $price; return $this; }
    public function getCommerce(): ?Commerce { return $this->commerce; }
    public function setCommerce(?Commerce $commerce): self { $this->commerce = $commerce; return $this; }
    public function getReservations(): Collection { return $this->reservations; }
    public function addReservation(Reservation $reservation): self {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations[] = $reservation;
            $reservation->setOffer($this);
        }
        return $this;
    }
    public function removeReservation(Reservation $reservation): self {
        if ($this->reservations->removeElement($reservation)) {
            if ($reservation->getOffer() === $this) {
                $reservation->setOffer(null);
            }
        }
        return $this;
    }
}
