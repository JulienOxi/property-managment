<?php

namespace App\Entity;

use App\Repository\LeaseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

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
    #[ORM\OneToMany(targetEntity: Tenant::class, mappedBy: 'lease')]
    private Collection $tenants;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $fromAt = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $toAt = null;

    /**
     * @var Collection<int, PropertyRent>
     */
    #[ORM\OneToMany(targetEntity: PropertyRent::class, mappedBy: 'lease')]
    private Collection $propertyRent;

    public function __construct()
    {
        $this->tenants = new ArrayCollection();
        $this->propertyRent = new ArrayCollection();
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
    public function getPropertyRent(): Collection
    {
        return $this->propertyRent;
    }

    public function addPropertyRent(PropertyRent $propertyRent): static
    {
        if (!$this->propertyRent->contains($propertyRent)) {
            $this->propertyRent->add($propertyRent);
            $propertyRent->setLease($this);
        }

        return $this;
    }

    public function removePropertyRent(PropertyRent $propertyRent): static
    {
        if ($this->propertyRent->removeElement($propertyRent)) {
            // set the owning side to null (unless already changed)
            if ($propertyRent->getLease() === $this) {
                $propertyRent->setLease(null);
            }
        }

        return $this;
    }
}
