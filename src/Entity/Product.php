<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Entity\Trait\BlameableTimestampableTrait;
use App\Repository\ProductRepository;
use Symfony\Component\Serializer\Attribute\Groups;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[ORM\Table(name: '`product`')]

#[
    ApiResource(
        operations: [
            new GetCollection(),
            new Post(
                security: 'is_granted("PUBLIC_ACCESS")',
                validationContext: ['groups' => ['Default', 'postValidation']],
            ),
            new Get(),
            new Patch(
                security: 'is_granted("PUBLIC_ACCESS")',
            ),
            new Delete(),
        ],
        normalizationContext: ['groups' => ['product:read']],
        denormalizationContext: ['groups' => ['product:create', 'product:update']],
        order: ['createdAt' => 'DESC', 'name' => 'ASC'],
        paginationEnabled: true,
    ),
]

class Product
{
    use BlameableTimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    #[Groups(['product:read', 'product:create', 'product:update'])]
    private ?string $name = null;

    #[ORM\Column(length: 25)]
    #[Groups(['product:read', 'product:create', 'product:update'])]
    private ?string $size = null;

    #[ORM\Column(length: 30)]
    #[Groups(['product:read', 'product:create', 'product:update'])]
    private ?string $colour = null;

    #[ORM\Column]
      #[Groups(['product:read', 'product:create', 'product:update'])]     
    private ?float $price = null;

    #[ORM\Column]
    #[Groups(['product:read', 'product:create', 'product:update'])]
    private ?int $stock = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getSize(): ?string
    {
        return $this->size;
    }

    public function setSize(string $size): static
    {
        $this->size = $size;

        return $this;
    }

    public function getColour(): ?string
    {
        return $this->colour;
    }

    public function setColour(string $colour): static
    {
        $this->colour = $colour;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function setStock(int $stock): static
    {
        $this->stock = $stock;

        return $this;
    }
}
