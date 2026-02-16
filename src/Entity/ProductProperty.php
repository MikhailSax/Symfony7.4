<?php

namespace App\Entity;

use App\Repository\ProductPropertyRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProductPropertyRepository::class)]
#[ORM\HasLifecycleCallbacks]
class ProductProperty
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank]
    #[ORM\ManyToOne(inversedBy: 'productProperties')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Product $product = null;

    #[Assert\NotBlank]
    #[ORM\ManyToOne(inversedBy: 'productProperties')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Properties $property = null;

    #[Assert\NotBlank]
    #[ORM\Column(length: 255)]
    private ?string $value = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\PrePersist]
    public function onCreate(): void
    {
        $now = new \DateTimeImmutable();
        $this->createdAt = $now;
        $this->updatedAt = $now;
    }

    #[ORM\PreUpdate]
    public function onUpdate(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getProperty(): ?Properties
    {
        return $this->property;
    }

    public function setProperty(?Properties $property): static
    {
        $this->property = $property;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): static
    {
        $this->value = $value;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }
}
