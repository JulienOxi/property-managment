<?php

namespace App\Entity;

use App\Enum\MortgageEnum;
use App\Enum\PropertyEnum;
use App\Enum\AccessRoleEnum;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\PropertyRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PropertyRepository::class)]
class Property
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(
        message: 'Le nom ne peut pas être vide',
    )]
    #[Assert\Length(
        min: 2,
        max: 100,
        minMessage: 'Le nom doit faire au minimum {{ limit }} caractères',
        maxMessage: 'Le nom peut faire au maximum {{ limit }} caractères',
    )]
    #[Assert\NotBlank(
        message: 'Le nom ne peut pas être vide',
    )]
    private string $name = '';

    #[ORM\Column(type: "text")]
    #[Assert\NotBlank(
        message: 'La description ne peut pas être vide',
    )]
    #[Assert\Length(
        min: 5,
        max: 1000000,
        minMessage: 'La description doit faire au minimum {{ limit }} caractères',
        maxMessage: 'La description peut faire au maximum {{ limit }} caractères',
    )]
    private string $description = '';

    #[ORM\Column(enumType: PropertyEnum::class)]
    private ?PropertyEnum $type = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Address $address = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    #[Assert\Positive(
        message: 'Le prix d\'achat doit être supérieur à 0',
    )]
    #[Assert\Regex(
        pattern: "/^(10(?:[.,]0{1,2})?|[0-9](?:[0-9']{0,2}(?:[0-9]{3})?)*(?:[.,][0-9]{1,2})?)$/",
        message: 'Le prix d\'achat doit contenir uniquement des chiffres et des virgules',
    )]
    private ?string $purchasePrice = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $purchaseDate = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    #[Assert\Regex(
        pattern: "/^(10(?:[.,]0{1,2})?|[0-9](?:[.,][0-9]{1,2})?)$/",
        message: 'Le taux hypothécaire doit contenir uniquement des chiffres et des virgules',
    )]
    private ?string $mortgageRate = null;

    #[ORM\Column(enumType: MortgageEnum::class, nullable: true)]
    private ?MortgageEnum $mortgageType = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $mortgageEndDate = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    #[Assert\Regex(
        pattern: "/^(10(?:[.,]0{1,2})?|[0-9](?:[.,][0-9]{1,2})?)$/",
        message: 'Le taux hypothécaire doit contenir uniquement des chiffres et des virgules',
    )]
    private ?string $mortgageRate2 = null;

    #[ORM\Column(enumType: MortgageEnum::class, nullable: true)]
    private ?MortgageEnum $mortgageType2 = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $mortgageEndDate2 = null;

    /**
     * @var Collection<int, AccessControl>
     */
    #[ORM\OneToMany(targetEntity: AccessControl::class, mappedBy: 'property', cascade: ['persist', 'remove'])]
    private Collection $accessControls;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Positive(
        message: 'Le EWID doit être supérieur à 0',
    )]
    private ?string $EWID = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Positive(
        message: 'Le EGID doit être supérieur à 0',
    )]
    private ?string $EGID = null;

    private ?Lease $actualLease= null;

    /**
     * @var Collection<int, FinancialEntry>
     */
    #[ORM\OneToMany(targetEntity: FinancialEntry::class, mappedBy: 'property')]
    private Collection $financialEntries;

    #[ORM\ManyToOne]
    private ?Bank $bank = null;

    private float $totalRents = 0;

    private float $totalExpenses = 0;

    private float $unpaidRents = 0;

    #[ORM\ManyToOne]
    private ?User $createdBy = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne]
    private ?User $updatedBy = null;

    /**
     * @var Collection<int, UploadFile>
     */
    #[ORM\OneToMany(targetEntity: UploadFile::class, mappedBy: 'property')]
    private Collection $uploadFiles;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $slug = null;
    /**
     * @var Collection<int, Folder>
     */
    #[ORM\OneToMany(targetEntity: Folder::class, mappedBy: 'property')]
    private Collection $folders;

    /**
     * @var Collection<int, Lease>
     */
    #[ORM\OneToMany(targetEntity: Lease::class, mappedBy: 'property', cascade:["persist", "remove"])]
    private Collection $leases;

    #[ORM\Column(type: Types::DECIMAL, precision: 6, scale: 2, nullable: true)]
    private ?string $ownerChargesDepositAmount = null;


    public function __construct()
    {
        $this->accessControls = new ArrayCollection();
        $this->AccessControl = new ArrayCollection();
        $this->financialEntries = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
        $this->uploadFiles = new ArrayCollection();
        $this->folders = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): string
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


    public function getMortgageRate2(): ?string
    {
        return $this->mortgageRate2;
    }

    public function setMortgageRate2(?string $mortgageRate2): static
    {
        $this->mortgageRate2 = $mortgageRate2;

        return $this;
    }

    /**
     * @return MortgageEnum[]|null
     */
    public function getMortgageType2(): ?MortgageEnum
    {
        return $this->mortgageType2;
    }

    public function setMortgageType2(?MortgageEnum $mortgageType2): static
    {
        $this->mortgageType2 = $mortgageType2;

        return $this;
    }

    public function getMortgageEndDate2(): ?\DateTimeInterface
    {
        return $this->mortgageEndDate2;
    }

    public function setMortgageEndDate2(?\DateTimeInterface $mortgageEndDate2): static
    {
        $this->mortgageEndDate2 = $mortgageEndDate2;

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

    public function getActualLease(): ?Lease
    {
        return $this->actualLease;
    }

    public function setActualLease(?Lease $actualLease): static
    {
        $this->actualLease = $actualLease;

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
    
    public function getTotalExpenses(): float
    {
        return $this->totalExpenses;
    }

    public function setTotalExpenses(float $totalExpenses): static
    {
        $this->totalExpenses = $totalExpenses;

        return $this;
    }

    public function getUnpaidRents(): float
    {
        return $this->unpaidRents;
    }

    public function setUnpaidRents(float $unpaidRents): static
    {
        $this->unpaidRents = $unpaidRents;

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
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getUpdatedBy(): ?User
    {
        return $this->updatedBy;
    }

    public function setUpdatedBy(?User $updatedBy): static
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    public function getOwner(): ?User
    {
        foreach ($this->accessControls as $accessControl) {
            if ($accessControl->getRole() === AccessRoleEnum::OWNER) {
                return $accessControl->getGrantedUser();
            }
        }
        return null;
    }

    /**
     * @return Collection<int, UploadFile>
     */
    public function getUploadFiles(): Collection
    {
        return $this->uploadFiles;
    }

    public function addUploadFile(UploadFile $uploadFile): static
    {
        if (!$this->uploadFiles->contains($uploadFile)) {
            $this->uploadFiles->add($uploadFile);
            $uploadFile->setProperty($this);
        }

        return $this;
    }

    public function removeUploadFile(UploadFile $uploadFile): static
    {
        if ($this->uploadFiles->removeElement($uploadFile)) {
            // set the owning side to null (unless already changed)
            if ($uploadFile->getProperty() === $this) {
                $uploadFile->setProperty(null);
            }
        }

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

    /**
     * @return Collection<int, Folder>
     */
    public function getFolders(): Collection
    {
        return $this->folders;
    }

    public function addFolder(Folder $folder): static
    {
        if (!$this->folders->contains($folder)) {
            $this->folders->add($folder);
            $folder->setProperty($this);
        }

        return $this;
    }

    public function removeFolder(Folder $folder): static
    {
        if ($this->folders->removeElement($folder)) {
            // set the owning side to null (unless already changed)
            if ($folder->getProperty() === $this) {
                $folder->setProperty(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Lease>
     */
    public function getLeases(): Collection
    {
        return $this->leases;
    }

    public function addLease(Lease $lease): static
    {
        if (!$this->leases->contains($lease)) {
            $this->leases->add($lease);
            $lease->setProperty($this);
        }

        return $this;
    }

    public function removeLease(Lease $lease): static
    {
        if ($this->leases->removeElement($lease)) {
            // set the owning side to null (unless already changed)
            if ($lease->getProperty() === $this) {
                $lease->setProperty(null);
            }
        }

        return $this;
    }

    public function getOwnerChargesDepositAmount(): ?string
    {
        return $this->ownerChargesDepositAmount;
    }

    public function setOwnerChargesDepositAmount(?string $ownerChargesDepositAmount): static
    {
        $this->ownerChargesDepositAmount = $ownerChargesDepositAmount;

        return $this;
    }
}
