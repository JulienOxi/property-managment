<?php

namespace App\Entity;

use App\Repository\LeaseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: LeaseRepository::class)]
class Lease
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
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

    /**
     * @var Collection<int, PropertyRent>
     */
    #[ORM\OneToMany(targetEntity: PropertyRent::class, mappedBy: 'lease', cascade:["persist", "remove"])]
    private Collection $propertyRents;

    #[ORM\ManyToOne(inversedBy: 'leases')]
    private ?User $createdBy = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $slug = null;

    public function __construct()
    {
        $this->tenants = new ArrayCollection();
        $this->propertyRents = new ArrayCollection();
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
            $propertyRent->setLease($this);
        }

        return $this;
    }

    public function removePropertyRent(PropertyRent $propertyRent): static
    {
        if ($this->propertyRents->removeElement($propertyRent)) {
            // set the owning side to null (unless already changed)
            if ($propertyRent->getLease() === $this) {
                $propertyRent->setLease(null);
            }
        }

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
}
