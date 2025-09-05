<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\CommerceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\User;
use App\Entity\Photo;
use App\Entity\Reservation;

#[ORM\Entity(repositoryClass: CommerceRepository::class)]
#[ApiResource]
class Commerce
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null; // hôtel, camping, attraction, etc.

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $address = null;

    #[ORM\Column(length: 255)]
    private ?string $city = null;

    #[ORM\Column(length: 255)]
    private ?string $country = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    // 🔹 Relation avec l’utilisateur (commerçant)
    #[ORM\ManyToOne(inversedBy: 'commerces')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $commercant = null;

    // 🔹 Relation avec les photos
    #[ORM\OneToMany(targetEntity: Photo::class, mappedBy: 'commerce', cascade: ['persist', 'remove'])]
    private Collection $photos;

    // 🔹 Relation avec les réservations
    #[ORM\OneToMany(targetEntity: Reservation::class, mappedBy: 'commerce', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $reservations;

    /**
     * @var Collection<int, Review>
     */
    #[ORM\OneToMany(targetEntity: Review::class, mappedBy: 'commerce')]
    private Collection $reviews;

    /**
     * @var Collection<int, FeaturePhare>
     */
    #[ORM\OneToMany(targetEntity: FeaturePhare::class, mappedBy: 'commerce')]
    private Collection $featurePhares;

    public function __construct()
    {
        $this->photos = new ArrayCollection();
        $this->reservations = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->reviews = new ArrayCollection();
        $this->featurePhares = new ArrayCollection();
    }

    // -----------------------
    // Getters / Setters
    // -----------------------

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): static
    {
        $this->address = $address;
        return $this;
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

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): static
    {
        $this->country = $country;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getCommercant(): ?User
    {
        return $this->commercant;
    }

    public function setCommercant(?User $commercant): static
    {
        $this->commercant = $commercant;
        return $this;
    }

    /** @return Collection<int, Photo> */
    public function getPhotos(): Collection
    {
        return $this->photos;
    }

    public function addPhoto(Photo $photo): static
    {
        if (!$this->photos->contains($photo)) {
            $this->photos->add($photo);
            $photo->setCommerce($this);
        }
        return $this;
    }

    public function removePhoto(Photo $photo): static
    {
        if ($this->photos->removeElement($photo)) {
            if ($photo->getCommerce() === $this) {
                $photo->setCommerce(null);
            }
        }
        return $this;
    }

    /** @return Collection<int, Reservation> */
    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    public function addReservation(Reservation $reservation): static
    {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations->add($reservation);
            $reservation->setCommerce($this);
        }
        return $this;
    }

    public function removeReservation(Reservation $reservation): static
    {
        if ($this->reservations->removeElement($reservation)) {
            if ($reservation->getCommerce() === $this) {
                $reservation->setCommerce(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, Review>
     */
    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    public function addReview(Review $review): static
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews->add($review);
            $review->setCommerce($this);
        }

        return $this;
    }

    public function removeReview(Review $review): static
    {
        if ($this->reviews->removeElement($review)) {
            // set the owning side to null (unless already changed)
            if ($review->getCommerce() === $this) {
                $review->setCommerce(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, FeaturePhare>
     */
    public function getFeaturePhares(): Collection
    {
        return $this->featurePhares;
    }

    public function addFeaturePhare(FeaturePhare $featurePhare): static
    {
        if (!$this->featurePhares->contains($featurePhare)) {
            $this->featurePhares->add($featurePhare);
            $featurePhare->setCommerce($this);
        }

        return $this;
    }

    public function removeFeaturePhare(FeaturePhare $featurePhare): static
    {
        if ($this->featurePhares->removeElement($featurePhare)) {
            // set the owning side to null (unless already changed)
            if ($featurePhare->getCommerce() === $this) {
                $featurePhare->setCommerce(null);
            }
        }

        return $this;
    }
}
