<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\AddressRepository;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AddressRepository::class)]
class Address
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 25, nullable: true)]
    #[Assert\Regex(
        pattern: "/^(\b(0041|0)|\B\+41)(\s?\(0\))?(\s)?[1-9]{2}(\s)?[0-9]{3}(\s)?[0-9]{2}(\s)?[0-9]{2}\b$/",
        message: 'Le numéro de téléphone doit avoir le format d\'un numéro de téléphone suisse',
    )]
    private ?string $mobilePhone = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(
        min: 2,
        max: 255,
        minMessage: 'Le nom de la rue doit faire au minimum {{ limit }} caractères',
        maxMessage: 'Le nom de la rue peut faire au maximum {{ limit }} caractères',
    )]
    #[Assert\NotBlank(
        message: 'La rue ne peut pas être vide',
    )]
    private ?string $street = null;

    #[ORM\Column(length: 10, nullable: true)]
    #[Assert\Regex(
        pattern: "/^\d{1,4}[A-Za-z]?$/",
        message: 'Le numéro n\'est pas valide',
    )]
    #[Assert\NotBlank(
        message: 'Le numéro de rue ne peut pas être vide',
    )]
    private ?string $streetNumber = null;

    #[ORM\Column(type: Types::SMALLINT)]
    #[Assert\Range(
        min: 1000,
        max: 9999,
        notInRangeMessage: 'Le code postal doit avoir un format de 4 chiffres',
    )]
    #[Assert\NotBlank(
        message: 'Le code postal ne peut pas être vide',
    )]
    private ?int $zipCode = null;

    #[ORM\Column(length: 100)]
    #[Assert\Length(
        min: 2,
        max: 100,
        minMessage: 'La ville doit faire au minimum {{ limit }} caractères',
        maxMessage: 'La ville peut faire au maximum {{ limit }} caractères',
    )]
    #[Assert\NotBlank(
        message: 'Le nom de la ville ne peut pas être vide',
    )]
    private ?string $city = null;

    #[ORM\Column(length: 25, nullable: true)]
    #[Assert\Regex(
        pattern: "/^(\b(0041|0)|\B\+41)(\s?\(0\))?(\s)?[1-9]{2}(\s)?[0-9]{3}(\s)?[0-9]{2}(\s)?[0-9]{2}\b$/",
        message: 'Le numéro de téléphone doit avoir le format d\'un numéro de téléphone suisse',
    )]
    private ?string $phone = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMobilePhone(): ?string
    {
        return $this->mobilePhone;
    }

    public function setMobilePhone(?string $mobilePhone): static
    {
        $this->mobilePhone = $mobilePhone;

        return $this;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(string $street): static
    {
        $this->street = $street;

        return $this;
    }

    public function getStreetNumber(): ?string
    {
        return $this->streetNumber;
    }

    public function setStreetNumber(?string $streetNumber): static
    {
        $this->streetNumber = $streetNumber;

        return $this;
    }

    public function getZipCode(): ?int
    {
        return $this->zipCode;
    }

    public function setZipCode(int $zipCode): static
    {
        $this->zipCode = $zipCode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }
}
