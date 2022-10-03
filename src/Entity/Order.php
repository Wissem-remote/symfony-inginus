<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::ARRAY)]
    private array $panier = [];

    #[ORM\Column]
    private ?int $total = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdateAt = null;

    #[ORM\Column(length: 255)]
    private ?string $reference = null;

    #[ORM\Column(type: Types::ARRAY)]
    private array $adressLivraison = [];

    #[ORM\Column(type: Types::ARRAY, nullable: true)]
    private array $adressFacturation = [];

    #[ORM\ManyToOne(inversedBy: 'orders')]
    private ?User $user = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $nameLivraison = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nameFacturation = null;

    #[ORM\Column(nullable: true)]
    private ?bool $state = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $stateSending = null;

    #[ORM\Column(nullable: true)]
    private ?int $quantity = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPanier(): array
    {
        return $this->panier;
    }

    public function setPanier(array $panier): self
    {
        $this->panier = $panier;

        return $this;
    }

    public function getTotal(): ?int
    {
        return $this->total;
    }

    public function setTotal(int $total): self
    {
        $this->total = $total;

        return $this;
    }

    public function getCreatedateAt(): ?\DateTimeImmutable
    {
        return $this->createdateAt;
    }

    public function setCreatedateAt(\DateTimeImmutable $createdateAt): self
    {
        $this->createdateAt = $createdateAt;

        return $this;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    public function getAdressLivraison(): array
    {
        return $this->adressLivraison;
    }

    public function setAdressLivraison(array $adressLivraison): self
    {
        $this->adressLivraison = $adressLivraison;

        return $this;
    }

    public function getAdressFacturation(): array
    {
        return $this->adressFacturation;
    }

    public function setAdressFacturation(?array $adressFacturation): self
    {
        $this->adressFacturation = $adressFacturation;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getNameLivraison(): ?string
    {
        return $this->nameLivraison;
    }

    public function setNameLivraison(string $nameLivraison): self
    {
        $this->nameLivraison = $nameLivraison;

        return $this;
    }

    public function getNameFacturation(): ?string
    {
        return $this->nameFacturation;
    }

    public function setNameFacturation(?string $nameFacturation): self
    {
        $this->nameFacturation = $nameFacturation;

        return $this;
    }

    public function isState(): ?bool
    {
        return $this->state;
    }

    public function setState(?bool $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getStateSending(): ?string
    {
        return $this->stateSending;
    }

    public function setStateSending(?string $stateSending): self
    {
        $this->stateSending = $stateSending;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(?int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }


}
