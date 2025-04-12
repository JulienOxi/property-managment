<?php

namespace App\Entity;

use App\Enum\MortgageAmortizationTypeEnum;
use App\Enum\MortgageBillingPeriodEnum;
use App\Enum\MortgageTypeEnum;
use App\Repository\MortgageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: MortgageRepository::class)]
class Mortgage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    #[Assert\NotNull]
    private ?\DateTimeImmutable $FromAt = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    #[Assert\NotNull]
    private ?\DateTimeImmutable $toAt = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Bank $bank = null;

    #[ORM\ManyToOne(inversedBy: 'mortgages')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Property $property = null;

    #[ORM\Column(enumType: MortgageBillingPeriodEnum::class)]
    private ?MortgageBillingPeriodEnum $billingPeriod = null;

    #[ORM\Column(enumType: MortgageTypeEnum::class)]
    private ?MortgageTypeEnum $mortgageType = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 3)]
    private ?string $rate = null;

    /**
     * @var Collection<int, FinancialEntry>
     */
    #[ORM\OneToMany(targetEntity: FinancialEntry::class, mappedBy: 'mortgage')]
    private Collection $financialEntries;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $amount = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2, nullable: true)]
    private ?string $amortization = null;

    #[ORM\Column(nullable: true, enumType: MortgageAmortizationTypeEnum::class)]
    private ?MortgageAmortizationTypeEnum $amortizationType = null;

    public function __construct()
    {
        $this->mortgagePayments = new ArrayCollection();
        $this->financialEntries = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFromAt(): ?\DateTimeImmutable
    {
        return $this->FromAt;
    }

    public function setFromAt(\DateTimeImmutable $FromAt): static
    {
        $this->FromAt = $FromAt;

        return $this;
    }

    public function getToAt(): ?\DateTimeImmutable
    {
        return $this->toAt;
    }

    public function setToAt(\DateTimeImmutable $toAt): static
    {
        $this->toAt = $toAt;

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

    public function getProperty(): ?Property
    {
        return $this->property;
    }

    public function setProperty(?Property $property): static
    {
        $this->property = $property;

        return $this;
    }

    public function getBillingPeriod(): ?MortgageBillingPeriodEnum
    {
        return $this->billingPeriod;
    }

    public function setBillingPeriod(MortgageBillingPeriodEnum $billingPeriod): static
    {
        $this->billingPeriod = $billingPeriod;

        return $this;
    }

    public function getMortgageType(): ?MortgageTypeEnum
    {
        return $this->mortgageType;
    }

    public function setMortgageType(MortgageTypeEnum $mortgageType): static
    {
        $this->mortgageType = $mortgageType;

        return $this;
    }

    public function getRate(): ?string
    {
        return $this->rate;
    }

    public function setRate(string $rate): static
    {
        $this->rate = $rate;

        return $this;
    }

    /**
     * @return Collection<int, FinancialEntry>
     */
    public function getFinancialEntries(): Collection
    {
        return $this->financialEntries;
    }

    public function addFinancialEntry(FinancialEntry $financialEntry): static
    {
        if (!$this->financialEntries->contains($financialEntry)) {
            $this->financialEntries->add($financialEntry);
            $financialEntry->setMortgage($this);
        }

        return $this;
    }

    public function removeFinancialEntry(FinancialEntry $financialEntry): static
    {
        if ($this->financialEntries->removeElement($financialEntry)) {
            // set the owning side to null (unless already changed)
            if ($financialEntry->getMortgage() === $this) {
                $financialEntry->setMortgage(null);
            }
        }

        return $this;
    }

    public function getAmount(): ?string
    {
        return $this->amount;
    }

    public function setAmount(?string $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    public function getAmortization(): ?string
    {
        return $this->amortization;
    }

    public function setAmortization(?string $amortization): static
    {
        $this->amortization = $amortization;

        return $this;
    }

    public function getAmortizationType(): ?MortgageAmortizationTypeEnum
    {
        return $this->amortizationType;
    }

    public function setAmortizationType(?MortgageAmortizationTypeEnum $amortizationType): static
    {
        $this->amortizationType = $amortizationType;

        return $this;
    }
}
