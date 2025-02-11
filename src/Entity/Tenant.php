<?php

namespace App\Entity;

use App\Repository\TenantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TenantRepository::class)]
class Tenant
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Assert\Length(
        min: 2,
        max: 50,
        minMessage: 'Le prénom doit faire au minimum {{ limit }} caractères',
        maxMessage: 'Le prénom peut faire au maximum {{ limit }} caractères',
    )]
    #[Assert\NotBlank(
        message: 'Le prénom ne peut pas être vide',
    )]
    private ?string $firstname = null;

    #[ORM\Column(length: 50)]
    #[Assert\Length(
        min: 2,
        max: 50,
        minMessage: 'Le nom doit faire au minimum {{ limit }} caractères',
        maxMessage: 'Le nom peut faire au maximum {{ limit }} caractères',
    )]
    #[Assert\NotBlank(
        message: 'Le nom ne peut pas être vide',
    )]
    private ?string $lastname = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Email(
        message: 'l\'email {{ value }} n\'est pas un email valide.',
    )]
    private ?string $email = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Assert\Regex(
        pattern: "/^(\b(0041|0)|\B\+41)(\s?\(0\))?(\s)?[1-9]{2}(\s)?[0-9]{3}(\s)?[0-9]{2}(\s)?[0-9]{2}\b$/",
        message: 'Le numéro de téléphone doit avoir le format d\'un numéro de téléphone suisse',
    )]
    private ?string $phoneNumber = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $rentalStartDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $rentalEndDate = null;

    #[ORM\ManyToOne(inversedBy: 'tenants')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Property $property = null;

    /**
     * @var Collection<int, PropertyRent>
     */
    #[ORM\OneToMany(targetEntity: PropertyRent::class, mappedBy: 'tenant', orphanRemoval: true)]
    private Collection $propertyRents;

    /**
     * @var Collection<int, FinancialEntry>
     */
    #[ORM\OneToMany(targetEntity: FinancialEntry::class, mappedBy: 'tenant')]
    private Collection $financialEntries;

    public function __construct()
    {
        $this->propertyRents = new ArrayCollection();
        $this->financialEntries = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getFullName(): string
    {
        return $this->firstname . ' ' . $this->lastname;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?string $phoneNumber): static
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getRentalStartDate(): ?\DateTimeInterface
    {
        return $this->rentalStartDate;
    }

    public function setRentalStartDate(\DateTimeInterface $rentalStartDate): static
    {
        $this->rentalStartDate = $rentalStartDate;

        return $this;
    }

    public function getRentalEndDate(): ?\DateTimeInterface
    {
        return $this->rentalEndDate;
    }

    public function setRentalEndDate(\DateTimeInterface $rentalEndDate): static
    {
        $this->rentalEndDate = $rentalEndDate;

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
            $propertyRent->setTenant($this);
        }

        return $this;
    }

    public function removePropertyRent(PropertyRent $propertyRent): static
    {
        if ($this->propertyRents->removeElement($propertyRent)) {
            // set the owning side to null (unless already changed)
            if ($propertyRent->getTenant() === $this) {
                $propertyRent->setTenant(null);
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
            $financialEntry->setTenant($this);
        }

        return $this;
    }

    public function removeFinancialEntry(FinancialEntry $financialEntry): static
    {
        if ($this->financialEntries->removeElement($financialEntry)) {
            // set the owning side to null (unless already changed)
            if ($financialEntry->getTenant() === $this) {
                $financialEntry->setTenant(null);
            }
        }

        return $this;
    }
}
