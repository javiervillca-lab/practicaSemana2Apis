<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\SaleDetailRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: SaleDetailRepository::class)]
#[ApiResource(operations: [])]
class SaleDetail
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[Groups(['sale:read', 'sale:create', 'sale:update'])]
    private ?int $quantity = null;

    #[ORM\Column]
    #[Groups(['sale:read', 'sale:create', 'sale:update'])]
    private ?float $unitPrice = null;

    #[ORM\Column]
    #[Groups(['sale:read', 'sale:create', 'sale:update'])]
    private ?float $lineTotal = null;

    #[ORM\ManyToOne(inversedBy: 'saleDetails')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['sale:create', 'sale:update'])]
    private ?Sale $sale = null;

    #[ORM\ManyToOne(inversedBy: 'saleDetails')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['sale:read', 'sale:create', 'sale:update'])]
    private ?Product $product = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getUnitPrice(): ?float
    {
        return $this->unitPrice;
    }

    public function setUnitPrice(float $unitPrice): static
    {
        $this->unitPrice = $unitPrice;

        return $this;
    }

    public function getLineTotal(): ?float
    {
        return $this->lineTotal;
    }

    public function setLineTotal(float $lineTotal): static
    {
        $this->lineTotal = $lineTotal;

        return $this;
    }

    public function getSale(): ?Sale
    {
        return $this->sale;
    }

    public function setSale(?Sale $sale): static
    {
        $this->sale = $sale;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): static
    {
        $this->product = $product;

        return $this;
    }
}
