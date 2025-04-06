<?php

namespace App\Entity;

use App\Enum\RentalFeeEnum;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\LeaseRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: LeaseRepository::class)]
class Lease
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'leases')]
    private ?Property $property = null;

    /**
     * @var Collection<int, Tenant>
     */
    #[Assert\Valid]
    #[ORM\OneToMany(targetEntity: Tenant::class, mappedBy: 'lease', cascade:["persist", "remove"])]
    private Collection $tenants;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $fromAt = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $toAt = null;

    #[ORM\ManyToOne(inversedBy: 'leases')]
    private ?User $createdBy = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $slug = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 6, scale: 2, nullable: true)]
    #[Assert\GreaterThanOrEqual(0)]
    #[Assert\Regex(pattern: "/^\d+(?:[\.,]\d{0,2})?$/", message: "Le montant peut avoir au maximum 2 décimales.")]
    private ?string $rentAmount = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 6, scale: 2, nullable: true)]
    #[Assert\GreaterThanOrEqual(0)]
    #[Assert\Regex(pattern: "/^\d+(?:[\.,]\d{0,2})?$/", message: "Le montant peut avoir au maximum 2 décimales.")]
    private ?string $parkingAmount = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 6, scale: 2, nullable: true)]
    #[Assert\GreaterThanOrEqual(0)]
    #[Assert\Regex(pattern: "/^\d+(?:[\.,]\d{0,2})?$/", message: "Le montant peut avoir au maximum 2 décimales.")]    
    private ?string $feeAmount = null;

    #[ORM\Column(enumType: RentalFeeEnum::class)]
    private ?RentalFeeEnum $feeType = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 6, scale: 2, nullable: true)]
    #[Assert\GreaterThanOrEqual(0)]
    #[Assert\Regex(pattern: "/^\d+(?:[\.,]\d{0,2})?$/", message: "Le montant peut avoir au maximum 2 décimales.")]    
    private ?string $variousAmount = null;

    /**
     * @var Collection<int, FinancialEntry>
     */
    #[ORM\OneToMany(targetEntity: FinancialEntry::class, mappedBy: 'lease')]
    private Collection $financialEntries;

    private ?array $infos = [];

    #[ORM\ManyToOne]
    private ?Bank $bank = null;

    public function __construct()
    {
        $this->tenants = new ArrayCollection();
        $this->propertyRents = new ArrayCollection();
        $this->financialEntries = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * @return Collection<int, Tenant>
     */
    public function getTenants(): Collection
    {
        return $this->tenants;
    }

    public function addTenant(Tenant $tenant): static
    {
        if (!$this->tenants->contains($tenant)) {
            $this->tenants->add($tenant);
            $tenant->setLease($this);
        }

        return $this;
    }

    public function removeTenant(Tenant $tenant): static
    {
        if ($this->tenants->removeElement($tenant)) {
            // set the owning side to null (unless already changed)
            if ($tenant->getLease() === $this) {
                $tenant->setLease(null);
            }
        }

        return $this;
    }

    public function getFromAt(): ?\DateTimeInterface
    {
        return $this->fromAt;
    }

    public function setFromAt(\DateTimeInterface $fromAt): static
    {
        $this->fromAt = $fromAt;

        return $this;
    }

    public function getToAt(): ?\DateTimeInterface
    {
        return $this->toAt;
    }

    public function setToAt(\DateTimeInterface $toAt): static
    {
        $this->toAt = $toAt;

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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getRentAmount(): ?string
    {
        return $this->rentAmount;
    }

    public function setRentAmount(?string $rentAmount): static
    {
        $this->rentAmount = $rentAmount;

        return $this;
    }

    public function getParkingAmount(): ?string
    {
        return $this->parkingAmount;
    }

    public function setParkingAmount(?string $parkingAmount): static
    {
        $this->parkingAmount = $parkingAmount;

        return $this;
    }

    public function getFeeAmount(): ?string
    {
        return $this->feeAmount;
    }

    public function setFeeAmount(?string $feeAmount): static
    {
        $this->feeAmount = $feeAmount;

        return $this;
    }

    public function getFeeType(): ?RentalFeeEnum
    {
        return $this->feeType;
    }

    public function setFeeType(?RentalFeeEnum $feeType): static
    {
        $this->feeType = $feeType;

        return $this;
    }

    public function getVariousAmount(): ?string
    {
        return $this->variousAmount;
    }

    public function setVariousAmount(?string $variousAmount): static
    {
        $this->variousAmount = $variousAmount;

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
            $financialEntry->setLease($this);
        }

        return $this;
    }

    public function removeFinancialEntry(FinancialEntry $financialEntry): static
    {
        if ($this->financialEntries->removeElement($financialEntry)) {
            // set the owning side to null (unless already changed)
            if ($financialEntry->getLease() === $this) {
                $financialEntry->setLease(null);
            }
        }

        return $this;
    }

    public function getinfos(): ?array
    {
        return $this->infos;
    }
    public function setInfos(array $infos): static
    {
        $this->infos = $infos;

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
}
