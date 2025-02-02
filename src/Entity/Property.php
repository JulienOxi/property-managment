<?php

namespace App\Entity;

use App\Enum\MortgageEnum;
use App\Enum\PropertyEnum;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\PropertyRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: PropertyRepository::class)]
class Property
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column(enumType: PropertyEnum::class)]
    private ?PropertyEnum $type = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Address $address = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $purchasePrice = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $purchaseDate = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $mortgageRate = null;

    #[ORM\Column(enumType: MortgageEnum::class, nullable: true)]
    private ?MortgageEnum $mortgageType = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $mortgageEndDate = null;

    /**
     * @var Collection<int, AccessControl>
     */
    #[ORM\OneToMany(targetEntity: AccessControl::class, mappedBy: 'property', cascade: ['persist', 'remove'])]
    private Collection $accessControls;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $EWID = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $EGID = null;

    /**
     * @var Collection<int, PropertyRent>
     */
    #[ORM\OneToMany(targetEntity: PropertyRent::class, mappedBy: 'property')]
    private Collection $propertyRents;

    /**
     * @var Collection<int, Tenant>
     */
    #[ORM\OneToMany(targetEntity: Tenant::class, mappedBy: 'property', orphanRemoval: true)]
    private Collection $tenants;

    /**
     * @var Collection<int, FinancialEntry>
     */
    #[ORM\OneToMany(targetEntity: FinancialEntry::class, mappedBy: 'property')]
    private Collection $financialEntries;

    #[ORM\ManyToOne]
    private ?Bank $bank = null;

    private float $totalRents = 0;

    #[ORM\ManyToOne]
    private ?User $createdBy = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $UpdatedAt = null;

    #[ORM\ManyToOne]
    private ?User $UpdatedBy = null;


    public function __construct()
    {
        $this->accessControls = new ArrayCollection();
        $this->AccessControl = new ArrayCollection();
        $this->propertyRents = new ArrayCollection();
        $this->tenants = new ArrayCollection();
        $this->financialEntries = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->createdAt = new \DateTimeImmutable();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getType(): ?PropertyEnum
    {
        return $this->type;
    }

    public function setType(PropertyEnum $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getAddress(): ?Address
    {
        return $this->address;
    }

    public function setAddress(Address $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getPurchasePrice(): ?string
    {
        return $this->purchasePrice;
    }

    public function setPurchasePrice(?string $purchasePrice): static
    {
        $this->purchasePrice = $purchasePrice;

        return $this;
    }

    public function getPurchaseDate(): ?\DateTimeInterface
    {
        return $this->purchaseDate;
    }

    public function setPurchaseDate(\DateTimeInterface $purchaseDate): static
    {
        $this->purchaseDate = $purchaseDate;

        return $this;
    }

    public function getMortgageRate(): ?string
    {
        return $this->mortgageRate;
    }

    public function setMortgageRate(?string $mortgageRate): static
    {
        $this->mortgageRate = $mortgageRate;

        return $this;
    }

    /**
     * @return MortgageEnum[]|null
     */
    public function getMortgageType(): ?MortgageEnum
    {
        return $this->mortgageType;
    }

    public function setMortgageType(?MortgageEnum $mortgageType): static
    {
        $this->mortgageType = $mortgageType;

        return $this;
    }

    public function getMortgageEndDate(): ?\DateTimeInterface
    {
        return $this->mortgageEndDate;
    }

    public function setMortgageEndDate(?\DateTimeInterface $mortgageEndDate): static
    {
        $this->mortgageEndDate = $mortgageEndDate;

        return $this;
    }

    /**
     * @return Collection<int, AccessControl>
     */
    public function getAccessControls(): Collection
    {
        return $this->accessControls;
    }

    public function addAccessControl(AccessControl $accessControl): static
    {
        if (!$this->accessControls->contains($accessControl)) {
            $this->accessControls->add($accessControl);
        }

        return $this;
    }

    public function removeAccessControl(AccessControl $accessControl): static
    {
        $this->accessControls->removeElement($accessControl);

        return $this;
    }

    /**
     * @return Collection<int, AccessControl>
     */
    public function getAccessControl(): Collection
    {
        return $this->AccessControl;
    }

    public function getEWID(): ?string
    {
        return $this->EWID;
    }

    public function setEWID(?string $EWID): static
    {
        $this->EWID = $EWID;

        return $this;
    }

    public function getEGID(): ?string
    {
        return $this->EGID;
    }

    public function setEGID(?string $EGID): static
    {
        $this->EGID = $EGID;

        return $this;
    }

    /**
     * @return Collection<int, PropertyRent>
     */
    public function getPropertyRents(): Collection
    {
        return $this->propertyRents;
    }

    public function addPropertyRent(PropertyRent $propertyRent): static
    {
        if (!$this->propertyRents->contains($propertyRent)) {
            $this->propertyRents->add($propertyRent);
            $propertyRent->setProperty($this);
        }

        return $this;
    }

    public function removePropertyRent(PropertyRent $propertyRent): static
    {
        if ($this->propertyRents->removeElement($propertyRent)) {
            // set the owning side to null (unless already changed)
            if ($propertyRent->getProperty() === $this) {
                $propertyRent->setProperty(null);
            }
        }

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
            $tenant->setProperty($this);
        }

        return $this;
    }

    public function removeTenant(Tenant $tenant): static
    {
        if ($this->tenants->removeElement($tenant)) {
            // set the owning side to null (unless already changed)
            if ($tenant->getProperty() === $this) {
                $tenant->setProperty(null);
            }
        }

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
            $financialEntry->setProperty($this);
        }

        return $this;
    }

    public function removeFinancialEntry(FinancialEntry $financialEntry): static
    {
        if ($this->financialEntries->removeElement($financialEntry)) {
            // set the owning side to null (unless already changed)
            if ($financialEntry->getProperty() === $this) {
                $financialEntry->setProperty(null);
            }
        }

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

    public function getTotalRents(): float
    {
        return $this->totalRents;
    }

    public function setTotalRents(float $totalRents): static
    {
        $this->totalRents = $totalRents;

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

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->UpdatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $UpdatedAt): static
    {
        $this->UpdatedAt = $UpdatedAt;

        return $this;
    }

    public function getUpdatedBy(): ?User
    {
        return $this->UpdatedBy;
    }

    public function setUpdatedBy(?User $UpdatedBy): static
    {
        $this->UpdatedBy = $UpdatedBy;

        return $this;
    }
}
