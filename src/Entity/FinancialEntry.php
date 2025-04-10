<?php

namespace App\Entity;

use App\Enum\FinancialCategoryEnum;
use App\Enum\TransactionEnum;
use App\Repository\FinancialEntryRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: FinancialEntryRepository::class)]
class FinancialEntry
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(enumType: TransactionEnum::class)]
    private ?TransactionEnum $type = null;

    #[ORM\Column(enumType: FinancialCategoryEnum::class)]
    private ?FinancialCategoryEnum $category = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\NotBlank]
    #[Assert\Type(
        type: "numeric",
        message: 'Veuillez entrer une valeur numérique'
    )]
    #[Assert\GreaterThanOrEqual(0)]
    #[Assert\Regex(pattern: "/^\d+(?:[\.,]\d{0,2})?$/", message: "Le montant peut avoir au maximum 2 décimales.")]
    private ?string $amount = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(
        min: 2,
        max: 255,
        minMessage: 'La description doit faire au minimum {{ limit }} caractères',
        maxMessage: 'La description peut faire au maximum {{ limit }} caractères',
    )]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'financialEntries')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Property $property = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $createdBy = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?bool $isPaid = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $paidAt = null;

    #[ORM\ManyToOne(inversedBy: 'financialEntries')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Bank $bank = null;

    #[ORM\ManyToOne(inversedBy: 'financialEntries')]
    private ?Lease $lease = null;

    #[ORM\ManyToOne(inversedBy: 'financialEntries')]
    private ?Mortgage $mortgage = null;

    public function __construct(){
        $this->createdAt = new \DateTimeImmutable();
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?TransactionEnum
    {
        return $this->type;
    }

    public function setType(TransactionEnum $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getCategory(): ?FinancialCategoryEnum
    {
        return $this->category;
    }

    public function setCategory(FinancialCategoryEnum $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getAmount(): ?string
    {
        return $this->amount;
    }

    public function setAmount(string $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getProperty(): ?Property
    {
        return $this->property;
    }

    public function setProperty(?Property $property): static
    {
        $this->property = $property;

        return $this;
    }

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): static
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function isPaid(): ?bool
    {
        return $this->isPaid;
    }

    public function setIsPaid(bool $isPaid): static
    {
        $this->isPaid = $isPaid;

        return $this;
    }

    public function getPaidAt(): ?\DateTimeImmutable
    {
        return $this->paidAt;
    }

    public function setPaidAt(\DateTimeImmutable $paidAt): static
    {
        $this->paidAt = $paidAt;

        return $this;
    }

    public function getBank(): ?Bank
    {
        return $this->bank;
    }

    public function setBank(?Bank $bank): static
    {
        $this->bank = $bank;

        return $this;
    }

    public function getLease(): ?Lease
    {
        return $this->lease;
    }

    public function setLease(?Lease $lease): static
    {
        $this->lease = $lease;

        return $this;
    }

    public function getMortgage(): ?Mortgage
    {
        return $this->mortgage;
    }

    public function setMortgage(?Mortgage $mortgage): static
    {
        $this->mortgage = $mortgage;

        return $this;
    }
}
