<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Repository\SaleRepository;
use App\Entity\Trait\BlameableTimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Serializer\Attribute\Groups;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SaleRepository::class)]
#[ORM\Table(name: '`sale`')]

#[
    ApiResource(
        operations: [
            new GetCollection(),
            new Post(),
            new Get(),
            new Patch(),
            new Delete(),
        ],
        normalizationContext: ['groups' => ['sale:read']],
        denormalizationContext: ['groups' => ['sale:create', 'sale:update']],
        order: ['createdAt' => 'DESC', 'name' => 'ASC'],
        paginationEnabled: true,
    ),
]

class Sale
{
    use BlameableTimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['sale:read', 'sale:create', 'sale:update'])]
    private ?\DateTime $saleDate = null;

    #[ORM\Column]
    #[Groups(['sale:read', 'sale:create', 'sale:update'])]
    private ?float $totalPrice = null;

    #[ORM\ManyToOne(inversedBy: 'sales')]
    #[Groups(['sale:read', 'sale:create', 'sale:update'])]
    private ?Customer $customer = null;

    #[ORM\Column(length: 50)]
    #[Groups(['sale:read', 'sale:create', 'sale:update'])]
    private ?string $state = null;

    /**
     * @var Collection<int, SaleDetail>
     */
    #[ORM\OneToMany(targetEntity: SaleDetail::class, mappedBy: 'sale')]
    private Collection $saleDetails;

    public function __construct()
    {
        $this->saleDetails = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSaleDate(): ?\DateTime
    {
        return $this->saleDate;
    }

    public function setSaleDate(\DateTime $saleDate): static
    {
        $this->saleDate = $saleDate;

        return $this;
    }

    public function getTotalPrice(): ?float
    {
        return $this->totalPrice;
    }

    public function setTotalPrice(float $totalPrice): static
    {
        $this->totalPrice = $totalPrice;

        return $this;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): static
    {
        $this->customer = $customer;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(string $state): static
    {
        $this->state = $state;

        return $this;
    }

    /**
     * @return Collection<int, SaleDetail>
     */
    public function getSaleDetails(): Collection
    {
        return $this->saleDetails;
    }

    public function addSaleDetail(SaleDetail $saleDetail): static
    {
        if (!$this->saleDetails->contains($saleDetail)) {
            $this->saleDetails->add($saleDetail);
            $saleDetail->setSale($this);
        }

        return $this;
    }

    public function removeSaleDetail(SaleDetail $saleDetail): static
    {
        if ($this->saleDetails->removeElement($saleDetail)) {
            // set the owning side to null (unless already changed)
            if ($saleDetail->getSale() === $this) {
                $saleDetail->setSale(null);
            }
        }

        return $this;
    }
}
