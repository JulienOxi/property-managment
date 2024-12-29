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
    #[ORM\ManyToMany(targetEntity: AccessControl::class, inversedBy: 'properties')]
    private Collection $accessControls;

    /**
     * @var Collection<int, AccessControl>
     */
    #[ORM\OneToMany(targetEntity: AccessControl::class, mappedBy: 'property', orphanRemoval: true)]
    private Collection $AccessControl;

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

    public function __construct()
    {
        $this->accessControls = new ArrayCollection();
        $this->AccessControl = new ArrayCollection();
        $this->propertyRents = new ArrayCollection();
        $this->tenants = new ArrayCollection();
        $this->financialEntries = new ArrayCollection();
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

    public function getType(): ?string
    {
        return $this->type?->value;
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
    public function getMortgageType(): ?string
    {
        return $this->mortgageType?->value;;
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
}
