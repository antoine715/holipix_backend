<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\ReservationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
#[ApiResource]
class Reservation
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Commerce $commerce = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    private ?Room $room = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    private ?Offer $offer = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $dateArrivee = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $dateDepart = null;

    #[ORM\Column(nullable: true)]
    private ?int $nombreAdultes = null;

    #[ORM\Column(nullable: true)]
    private ?int $nombreEnfants = null;

    #[ORM\Column]
    private ?int $nombreChambres = null;

    #[ORM\Column]
    private ?float $total = null;

    #[ORM\OneToOne(mappedBy: 'reservation', cascade: ['persist', 'remove'])]
    private ?Payment $payment = null;

    public function getId(): ?int { return $this->id; }
    public function getUser(): ?User { return $this->user; }
    public function setUser(?User $user): static { $this->user = $user; return $this; }
    public function getCommerce(): ?Commerce { return $this->commerce; }
    public function setCommerce(?Commerce $commerce): static { $this->commerce = $commerce; return $this; }
    public function getRoom(): ?Room { return $this->room; }
    public function setRoom(?Room $room): static { $this->room = $room; return $this; }
    public function getOffer(): ?Offer { return $this->offer; }
    public function setOffer(?Offer $offer): static { $this->offer = $offer; return $this; }
    public function getDateArrivee(): ?\DateTimeImmutable { return $this->dateArrivee; }
    public function setDateArrivee(\DateTimeImmutable $dateArrivee): static { $this->dateArrivee = $dateArrivee; return $this; }
    public function getDateDepart(): ?\DateTimeImmutable { return $this->dateDepart; }
    public function setDateDepart(\DateTimeImmutable $dateDepart): static { $this->dateDepart = $dateDepart; return $this; }
    public function getNombreAdultes(): ?int { return $this->nombreAdultes; }
    public function setNombreAdultes(?int $nombreAdultes): static { $this->nombreAdultes = $nombreAdultes; return $this; }
    public function getNombreEnfants(): ?int { return $this->nombreEnfants; }
    public function setNombreEnfants(?int $nombreEnfants): static { $this->nombreEnfants = $nombreEnfants; return $this; }
    public function getNombreChambres(): ?int { return $this->nombreChambres; }
    public function setNombreChambres(int $nombreChambres): static { $this->nombreChambres = $nombreChambres; return $this; }
    public function getTotal(): ?float { return $this->total; }
    public function setTotal(float $total): static { $this->total = $total; return $this; }
    public function getPayment(): ?Payment { return $this->payment; }
    public function setPayment(Payment $payment): static
    {
        if ($payment->getReservation() !== $this) $payment->setReservation($this);
        $this->payment = $payment;
        return $this;
    }
}
