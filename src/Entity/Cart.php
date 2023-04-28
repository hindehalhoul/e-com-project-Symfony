<?php

namespace App\Entity;

use App\Repository\CartRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CartRepository::class)]
class Cart
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user_id = null;

    #[ORM\ManyToMany(targetEntity: Product::class)]
    private Collection $Product_id;

    #[ORM\Column(length: 255)]
    private ?string $quantity = null;

    #[ORM\Column(length: 255)]
    private ?string $price = null;

    public function __construct()
    {
        $this->Product_id = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): ?User
    {
        return $this->user_id;
    }

    public function setUserId(User $user_id): self
    {
        $this->user_id = $user_id;

        return $this;
    }

    /**
     * @return Collection<int, Product>
     */
    public function getProductId(): Collection
    {
        return $this->Product_id;
    }

    public function addProductId(Product $productId): self
    {
        if (!$this->Product_id->contains($productId)) {
            $this->Product_id->add($productId);
        }

        return $this;
    }

    public function removeProductId(Product $productId): self
    {
        $this->Product_id->removeElement($productId);

        return $this;
    }

    public function getQuantity(): ?string
    {
        return $this->quantity;
    }

    public function setQuantity(string $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): self
    {
        $this->price = $price;

        return $this;
    }
}
