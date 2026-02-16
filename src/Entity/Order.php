<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
#[ORM\HasLifecycleCallbacks]
class Order
{
    public const STATUS_NEW = 'new';
    public const STATUS_QUOTE = 'quote';
    public const STATUS_LAYOUT = 'layout';
    public const STATUS_PRINT = 'print';
    public const STATUS_READY = 'ready';
    public const STATUS_DELIVERY = 'delivery';
    public const STATUS_ARCHIVE = 'archive';

    public const STATUSES = [
        self::STATUS_NEW => 'Новый',
        self::STATUS_QUOTE => 'Расчет',
        self::STATUS_LAYOUT => 'Макетирование',
        self::STATUS_PRINT => 'В печати',
        self::STATUS_READY => 'Готов к выдаче',
        self::STATUS_DELIVERY => 'Доставка',
        self::STATUS_ARCHIVE => 'Архив',
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $client = null;

    #[ORM\Column(length: 30)]
    private string $status = self::STATUS_NEW;

    #[ORM\Column(type: 'decimal', precision: 12, scale: 2)]
    private string $totalPrice = '0.00';

    #[ORM\Column(type: 'decimal', precision: 12, scale: 2)]
    private string $materialCost = '0.00';

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $comment = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    /** @var Collection<int, OrderItem> */
    #[ORM\OneToMany(targetEntity: OrderItem::class, mappedBy: 'order', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $items;

    /** @var Collection<int, FileAsset> */
    #[ORM\OneToMany(targetEntity: FileAsset::class, mappedBy: 'order', cascade: ['persist'])]
    private Collection $files;

    public function __construct()
    {
        $this->items = new ArrayCollection();
        $this->files = new ArrayCollection();
    }

    #[ORM\PrePersist]
    public function onCreate(): void
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
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

    public function getClient(): ?User
    {
        return $this->client;
    }

    public function setClient(?User $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getTotalPrice(): string
    {
        return $this->totalPrice;
    }

    public function setTotalPrice(string $totalPrice): self
    {
        $this->totalPrice = $totalPrice;

        return $this;
    }

    public function getMaterialCost(): string
    {
        return $this->materialCost;
    }

    public function setMaterialCost(string $materialCost): self
    {
        $this->materialCost = $materialCost;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

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

    public function getProfit(): float
    {
        return (float) $this->totalPrice - (float) $this->materialCost;
    }

    /** @return Collection<int, OrderItem> */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(OrderItem $item): self
    {
        if (!$this->items->contains($item)) {
            $this->items->add($item);
            $item->setOrder($this);
        }

        return $this;
    }

    public function removeItem(OrderItem $item): self
    {
        if ($this->items->removeElement($item) && $item->getOrder() === $this) {
            $item->setOrder(null);
        }

        return $this;
    }

    /** @return Collection<int, FileAsset> */
    public function getFiles(): Collection
    {
        return $this->files;
    }

    public function __toString(): string
    {
        return sprintf('#%d', $this->id ?? 0);
    }
}
