<?php

namespace App\Entity;

use App\Repository\PropertiesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PropertiesRepository::class)]
class Properties
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank]
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @var Collection<int, ProductProperty>
     */
    #[ORM\OneToMany(targetEntity: ProductProperty::class, mappedBy: 'property_id')]
    private Collection $productProperties;

    public function __construct()
    {
        $this->productProperties = new ArrayCollection();
    }

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
            $productProperty->setPropertyId($this);
        }

        return $this;
    }

    public function removeProductProperty(ProductProperty $productProperty): static
    {
        if ($this->productProperties->removeElement($productProperty)) {
            // set the owning side to null (unless already changed)
            if ($productProperty->getPropertyId() === $this) {
                $productProperty->setPropertyId(null);
            }
        }

        return $this;
    }
}
