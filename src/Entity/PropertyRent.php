<?php

namespace App\Entity;

use App\Enum\PropertyRentEnum;
use App\Enum\PropertyRentType;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\PropertyRentRepository;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PropertyRentRepository::class)]
class PropertyRent
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(
        message: 'La description ne peut pas être vide',
    )]
    #[Assert\Length(
        min: 5,
        max: 255,
        minMessage: 'La description doit faire au minimum {{ limit }} caractères',
        maxMessage: 'La description peut faire au maximum {{ limit }} caractères',
    )]
    private ?string $description = null;

    #[ORM\Column(enumType: PropertyRentEnum::class)]
    private ?PropertyRentEnum $type = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 7, scale: 2)]
    #[Assert\NotBlank]
    #[Assert\Type(
        type: "numeric",
        message: 'Veuillez entrer une valeur numérique'
    )]
    #[Assert\GreaterThanOrEqual(0)]
    #[Assert\Regex(pattern: "/^\d+(?:[\.,]\d{0,2})?$/", message: "Le montant peut avoir au maximum 2 décimales.")]
    private ?string $monthlyPrice = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'propertyRents')]
    private ?Lease $lease = null;

    public function __construct(){
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getType(): ?PropertyRentEnum
    {
        return $this->type;
    }

    public function setType(PropertyRentEnum $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getMonthlyPrice(): ?string
    {
        return $this->monthlyPrice;
    }

    public function setMonthlyPrice(string $monthlyPrice): static
    {
        $this->monthlyPrice = $monthlyPrice;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setcreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

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

    public function getLease(): ?Lease
    {
        return $this->lease;
    }

    public function setLease(?Lease $lease): static
    {
        $this->lease = $lease;

        return $this;
    }
}
