<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Entity\Trait\BlameableTimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Attribute\Groups;
use App\Repository\CustomerRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CustomerRepository::class)]
#[ORM\Table(name: '`customer`')]

#[
    ApiResource(
        operations: [
            new GetCollection(),
            new Post(),
            new Get(),
            new Patch(),
            new Delete(),
        ],
        normalizationContext: ['groups' => ['customer:read']],
        denormalizationContext: ['groups' => ['customer:create', 'customer:update']],
        order: ['createdAt' => 'DESC', 'nameFull' => 'ASC'],
        paginationEnabled: true,
    ),
]

class Customer
{
    use BlameableTimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 125)]
    #[Groups(['customer:read', 'customer:create', 'customer:update'])]
    private ?string $nameFull = null;

    #[ORM\Column(length: 25)]
    #[Groups(['customer:read', 'customer:create', 'customer:update'])]
    private ?string $nitCi = null;

    /**
     * @var Collection<int, Sale>
     */
    #[ORM\OneToMany(targetEntity: Sale::class, mappedBy: 'customer')]
    private Collection $sales;

    public function __construct()
    {
        $this->sales = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNameFull(): ?string
    {
        return $this->nameFull;
    }

    public function setNameFull(string $nameFull): static
    {
        $this->nameFull = $nameFull;

        return $this;
    }

    public function getNitCi(): ?string
    {
        return $this->nitCi;
    }

    public function setNitCi(string $nitCi): static
    {
        $this->nitCi = $nitCi;

        return $this;
    }

    /**
     * @return Collection<int, Sale>
     */
    public function getSales(): Collection
    {
        return $this->sales;
    }

    public function addSale(Sale $sale): static
    {
        if (!$this->sales->contains($sale)) {
            $this->sales->add($sale);
            $sale->setCustomer($this);
        }

        return $this;
    }

    public function removeSale(Sale $sale): static
    {
        if ($this->sales->removeElement($sale)) {
            // set the owning side to null (unless already changed)
            if ($sale->getCustomer() === $this) {
                $sale->setCustomer(null);
            }
        }

        return $this;
    }
}
