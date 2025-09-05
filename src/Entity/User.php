<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(type: 'string', length: 6, nullable: true)]
    private ?string $verificationCode = null;

    #[ORM\Column(type: 'boolean')]
    private bool $isVerified = false;

    // 🔹 Relations
    #[ORM\OneToMany(mappedBy: 'commercant', targetEntity: Commerce::class, cascade: ['persist', 'remove'])]
    private Collection $commerces;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Reservation::class, cascade: ['persist', 'remove'])]
    private Collection $reservations;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Photo::class, cascade: ['persist', 'remove'])]
    private Collection $photos;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Review::class)]
    private Collection $reviews;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Payment::class)]
    private Collection $payments;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Language::class)]
    private Collection $languages;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Admin::class)]
    private Collection $admins;

    public function __construct()
    {
        $this->commerces = new ArrayCollection();
        $this->reservations = new ArrayCollection();
        $this->photos = new ArrayCollection();
        $this->reviews = new ArrayCollection();
        $this->payments = new ArrayCollection();
        $this->languages = new ArrayCollection();
        $this->admins = new ArrayCollection();
    }

    // 🔹 Getters / Setters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    public function getVerificationCode(): ?string
    {
        return $this->verificationCode;
    }

    public function setVerificationCode(?string $verificationCode): static
    {
        $this->verificationCode = $verificationCode;
        return $this;
    }

    public function getIsVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;
        return $this;
    }

    #[\Deprecated]
    public function eraseCredentials(): void
    {
        // @deprecated, à retirer en Symfony 8
    }

    // 🔹 Relations Getters / Adders / Removers
    public function getCommerces(): Collection { return $this->commerces; }
    public function addCommerce(Commerce $commerce): static
    {
        if (!$this->commerces->contains($commerce)) {
            $this->commerces->add($commerce);
            $commerce->setCommercant($this);
        }
        return $this;
    }
    public function removeCommerce(Commerce $commerce): static
    {
        if ($this->commerces->removeElement($commerce)) {
            if ($commerce->getCommercant() === $this) {
                $commerce->setCommercant(null);
            }
        }
        return $this;
    }

    public function getReservations(): Collection { return $this->reservations; }
    public function addReservation(Reservation $reservation): static
    {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations->add($reservation);
            $reservation->setUser($this);
        }
        return $this;
    }
    public function removeReservation(Reservation $reservation): static
    {
        if ($this->reservations->removeElement($reservation)) {
            if ($reservation->getUser() === $this) {
                $reservation->setUser(null);
            }
        }
        return $this;
    }

    public function getPhotos(): Collection { return $this->photos; }
    public function addPhoto(Photo $photo): static
    {
        if (!$this->photos->contains($photo)) {
            $this->photos->add($photo);
            $photo->setUser($this);
        }
        return $this;
    }
    public function removePhoto(Photo $photo): static
    {
        if ($this->photos->removeElement($photo)) {
            if ($photo->getUser() === $this) {
                $photo->setUser(null);
            }
        }
        return $this;
    }

    public function getReviews(): Collection { return $this->reviews; }
    public function addReview(Review $review): static
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews->add($review);
            $review->setUser($this);
        }
        return $this;
    }
    public function removeReview(Review $review): static
    {
        if ($this->reviews->removeElement($review)) {
            if ($review->getUser() === $this) {
                $review->setUser(null);
            }
        }
        return $this;
    }

    public function getPayments(): Collection { return $this->payments; }
    public function addPayment(Payment $payment): static
    {
        if (!$this->payments->contains($payment)) {
            $this->payments->add($payment);
            $payment->setUser($this);
        }
        return $this;
    }
    public function removePayment(Payment $payment): static
    {
        if ($this->payments->removeElement($payment)) {
            if ($payment->getUser() === $this) {
                $payment->setUser(null);
            }
        }
        return $this;
    }

    public function getLanguages(): Collection { return $this->languages; }
    public function addLanguage(Language $language): static
    {
        if (!$this->languages->contains($language)) {
            $this->languages->add($language);
            $language->setUser($this);
        }
        return $this;
    }
    public function removeLanguage(Language $language): static
    {
        if ($this->languages->removeElement($language)) {
            if ($language->getUser() === $this) {
                $language->setUser(null);
            }
        }
        return $this;
    }

    public function getAdmins(): Collection { return $this->admins; }
    public function addAdmin(Admin $admin): static
    {
        if (!$this->admins->contains($admin)) {
            $this->admins->add($admin);
            $admin->setUser($this);
        }
        return $this;
    }
    public function removeAdmin(Admin $admin): static
    {
        if ($this->admins->removeElement($admin)) {
            if ($admin->getUser() === $this) {
                $admin->setUser(null);
            }
        }
        return $this;
    }
}
