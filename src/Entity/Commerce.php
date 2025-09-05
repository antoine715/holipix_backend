<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'commerce')]
class Commerce
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\OneToOne(targetEntity: User::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private User $commercant;

    #[ORM\Column(type: 'string')]
    private string $name;

    #[ORM\Column(type: 'string')]
    private string $type;

    #[ORM\Column(type: 'string')]
    private string $country;

    #[ORM\Column(type: 'string')]
    private string $city;

    #[ORM\Column(type: 'string')]
    private string $address;

    #[ORM\Column(type: 'string')]
    private string $phone;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $phoneFixe = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

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
}
