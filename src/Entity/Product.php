<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;


    #[Assert\NotBlank]
    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 30, max: 1000)]
    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 10, max: 255)]
    #[ORM\Column(length: 255)]
    private ?string $short_description = null;

    #[Assert\NotBlank]
    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[Assert\NotBlank]
    #[ORM\ManyToOne(inversedBy: 'products')]
    private ?Category $category_id = null;

    /**
     * @var Collection<int, Image>
     */
    #[ORM\OneToMany(targetEntity: Image::class, mappedBy: 'product')]
    private Collection $image_id;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updated_at = null;

    /**
     * @var Collection<int, ProductProperty>
     */
    #[ORM\OneToMany(targetEntity: ProductProperty::class, mappedBy: 'product_id')]
    private Collection $productProperties;

    /**
     * @var Collection<int, ProductPrice>
     */
    #[ORM\OneToMany(targetEntity: ProductPrice::class, mappedBy: 'product_id')]
    private Collection $productPrices;

    #[ORM\Column(nullable: true)]
    private ?bool $is_active = null;

    public function __construct()
    {
        $this->image_id = new ArrayCollection();
        $this->productProperties = new ArrayCollection();
        $this->productPrices = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getShortDescription(): ?string
    {
        return $this->short_description;
    }

    public function setShortDescription(string $short_description): static
    {
        $this->short_description = $short_description;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getCategoryId(): ?Category
    {
        return $this->category_id;
    }

    public function setCategoryId(?Category $category_id): static
    {
        $this->category_id = $category_id;

        return $this;
    }

    /**
     * @return Collection<int, Image>
     */
    public function getImageId(): Collection
    {
        return $this->image_id;
    }

    public function addImageId(Image $imageId): static
    {
        if (!$this->image_id->contains($imageId)) {
            $this->image_id->add($imageId);
            $imageId->setProduct($this);
        }

        return $this;
    }

    public function removeImageId(Image $imageId): static
    {
        if ($this->image_id->removeElement($imageId)) {
            // set the owning side to null (unless already changed)
            if ($imageId->getProduct() === $this) {
                $imageId->setProduct(null);
            }
        }

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeImmutable $updated_at): static
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    /**
     * @return Collection<int, ProductProperty>
     */
    public function getProductProperties(): Collection
    {
        return $this->productProperties;
    }

    public function addProductProperty(ProductProperty $productProperty): static
    {
        if (!$this->productProperties->contains($productProperty)) {
            $this->productProperties->add($productProperty);
            $productProperty->setProductId($this);
        }

        return $this;
    }

    public function removeProductProperty(ProductProperty $productProperty): static
    {
        if ($this->productProperties->removeElement($productProperty)) {
            // set the owning side to null (unless already changed)
            if ($productProperty->getProductId() === $this) {
                $productProperty->setProductId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ProductPrice>
     */
    public function getProductPrices(): Collection
    {
        return $this->productPrices;
    }

    public function addProductPrice(ProductPrice $productPrice): static
    {
        if (!$this->productPrices->contains($productPrice)) {
            $this->productPrices->add($productPrice);
            $productPrice->setProductId($this);
        }

        return $this;
    }

    public function removeProductPrice(ProductPrice $productPrice): static
    {
        if ($this->productPrices->removeElement($productPrice)) {
            // set the owning side to null (unless already changed)
            if ($productPrice->getProductId() === $this) {
                $productPrice->setProductId(null);
            }
        }

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->is_active;
    }

    public function setIsActive(?bool $is_active): static
    {
        $this->is_active = $is_active;

        return $this;
    }
}
