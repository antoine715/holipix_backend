<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\CommerceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;

#[ORM\Entity(repositoryClass: CommerceRepository::class)]
#[ORM\Table(name: 'commerce')]
#[ApiResource(
    normalizationContext: ['groups' => ['commerce:read']],
    denormalizationContext: ['groups' => ['commerce:write']]
)]
class Commerce
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: 'integer')]
    #[Groups(['commerce:read'])]
    private ?int $id = null;

    #[ORM\OneToOne(targetEntity: User::class, inversedBy: 'commerce', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['commerce:read', 'commerce:write'])]
    #[MaxDepth(1)]
    private User $commercant;

    #[ORM\Column(type: 'string')]
    #[Groups(['commerce:read', 'commerce:write'])]
    private string $name;

    #[ORM\Column(type: 'string')]
    #[Groups(['commerce:read', 'commerce:write'])]
    private string $type;

    #[ORM\Column(type: 'string')]
    #[Groups(['commerce:read', 'commerce:write'])]
    private string $country;

    #[ORM\Column(type: 'string')]
    #[Groups(['commerce:read', 'commerce:write'])]
    private string $city;

    #[ORM\Column(type: 'string')]
    #[Groups(['commerce:read', 'commerce:write'])]
    private string $address;

    #[ORM\Column(type: 'string')]
    #[Groups(['commerce:read', 'commerce:write'])]
    private string $phone;

    #[ORM\Column(type: 'string', nullable: true)]
    #[Groups(['commerce:read', 'commerce:write'])]
    private ?string $phoneFixe = null;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['commerce:read'])]
    private \DateTimeImmutable $createdAt;

    #[ORM\OneToMany(mappedBy: 'commerce', targetEntity: FeaturePhare::class, cascade: ['persist', 'remove'])]
    private Collection $featurePhares;

    #[ORM\OneToMany(mappedBy: 'commerce', targetEntity: Photo::class, cascade: ['persist', 'remove'])]
    private Collection $photos;

    #[ORM\OneToMany(mappedBy: 'commerce', targetEntity: Reservation::class, cascade: ['persist', 'remove'])]
    private Collection $reservations;

    #[ORM\OneToMany(mappedBy: 'commerce', targetEntity: Review::class, cascade: ['persist', 'remove'])]
    private Collection $reviews;

    #[ORM\OneToMany(mappedBy: 'commerce', targetEntity: Room::class, cascade: ['persist', 'remove'])]
    #[Groups(['commerce:read', 'commerce:write'])]
    private Collection $rooms;

    #[ORM\OneToMany(mappedBy: 'commerce', targetEntity: Offer::class, cascade: ['persist', 'remove'])]
    #[Groups(['commerce:read', 'commerce:write'])]
    private Collection $offers;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->featurePhares = new ArrayCollection();
        $this->photos = new ArrayCollection();
        $this->reservations = new ArrayCollection();
        $this->reviews = new ArrayCollection();
        $this->rooms = new ArrayCollection();
        $this->offers = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }
    public function getCommercant(): User { return $this->commercant; }
    public function setCommercant(User $user): self { $this->commercant = $user; return $this; }
    public function getName(): string { return $this->name; }
    public function setName(string $name): self { $this->name = $name; return $this; }
    public function getType(): string { return $this->type; }
    public function setType(string $type): self { $this->type = $type; return $this; }
    public function getCountry(): string { return $this->country; }
    public function setCountry(string $country): self { $this->country = $country; return $this; }
    public function getCity(): string { return $this->city; }
    public function setCity(string $city): self { $this->city = $city; return $this; }
    public function getAddress(): string { return $this->address; }
    public function setAddress(string $address): self { $this->address = $address; return $this; }
    public function getPhone(): string { return $this->phone; }
    public function setPhone(string $phone): self { $this->phone = $phone; return $this; }
    public function getPhoneFixe(): ?string { return $this->phoneFixe; }
    public function setPhoneFixe(?string $phoneFixe): self { $this->phoneFixe = $phoneFixe; return $this; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function setCreatedAt(\DateTimeImmutable $dt): self { $this->createdAt = $dt; return $this; }

    public function getFeaturePhares(): Collection { return $this->featurePhares; }
    public function addFeaturePhare(FeaturePhare $fp): self { 
        if (!$this->featurePhares->contains($fp)) { $this->featurePhares->add($fp); $fp->setCommerce($this); } 
        return $this; 
    }
    public function removeFeaturePhare(FeaturePhare $fp): self { 
        if ($this->featurePhares->removeElement($fp)) { if ($fp->getCommerce() === $this) $fp->setCommerce(null); } 
        return $this; 
    }

    public function getPhotos(): Collection { return $this->photos; }
    public function addPhoto(Photo $photo): self { 
        if (!$this->photos->contains($photo)) { $this->photos->add($photo); $photo->setCommerce($this); } 
        return $this; 
    }
    public function removePhoto(Photo $photo): self { 
        if ($this->photos->removeElement($photo)) { if ($photo->getCommerce() === $this) $photo->setCommerce(null); } 
        return $this; 
    }

    public function getReservations(): Collection { return $this->reservations; }
    public function addReservation(Reservation $reservation): self { 
        if (!$this->reservations->contains($reservation)) { $this->reservations->add($reservation); $reservation->setCommerce($this); } 
        return $this; 
    }
    public function removeReservation(Reservation $reservation): self { 
        if ($this->reservations->removeElement($reservation)) { if ($reservation->getCommerce() === $this) $reservation->setCommerce(null); } 
        return $this; 
    }

    public function getReviews(): Collection { return $this->reviews; }
    public function addReview(Review $review): self { 
        if (!$this->reviews->contains($review)) { $this->reviews->add($review); $review->setCommerce($this); } 
        return $this; 
    }
    public function removeReview(Review $review): self { 
        if ($this->reviews->removeElement($review)) { if ($review->getCommerce() === $this) $review->setCommerce(null); } 
        return $this; 
    }

    public function getRooms(): Collection { return $this->rooms; }
    public function addRoom(Room $room): self { 
        if (!$this->rooms->contains($room)) { $this->rooms->add($room); $room->setCommerce($this); } 
        return $this; 
    }
    public function removeRoom(Room $room): self { 
        if ($this->rooms->removeElement($room)) { if ($room->getCommerce() === $this) $room->setCommerce(null); } 
        return $this; 
    }

    public function getOffers(): Collection { return $this->offers; }
    public function addOffer(Offer $offer): self { 
        if (!$this->offers->contains($offer)) { $this->offers->add($offer); $offer->setCommerce($this); } 
        return $this; 
    }
    public function removeOffer(Offer $offer): self { 
        if ($this->offers->removeElement($offer)) { if ($offer->getCommerce() === $this) $offer->setCommerce(null); } 
        return $this; 
    }
}
