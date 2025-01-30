<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Scheb\TwoFactorBundle\Model\Email\TwoFactorInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'Un compte existe déjà avec cette adresse email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface, TwoFactorInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    #[ORM\Column(length: 255, nullable:true)]
    private ?string $authCode = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?UserProfile $profile = null;

    /**
     * @var Collection<int, AccessControl>
     */
    #[ORM\OneToMany(targetEntity: AccessControl::class, mappedBy: 'grantedUser', orphanRemoval: true)]
    private Collection $accessControls;

    /**
     * @var Collection<int, PropertyRent>
     */
    #[ORM\OneToMany(targetEntity: PropertyRent::class, mappedBy: 'createdBy', orphanRemoval: true)]
    private Collection $propertyRents;

    /**
     * @var Collection<int, UploadFile>
     */
    #[ORM\OneToMany(targetEntity: UploadFile::class, mappedBy: 'updatedBy')]
    private Collection $uploadFiles;

    #[ORM\Column]
    private bool $isVerified = false;

    public function __construct()
    {
        $this->accessControls = new ArrayCollection();
        $this->propertyRents = new ArrayCollection();
        $this->uploadFiles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getProfile(): ?UserProfile
    {
        return $this->profile;
    }

    public function setProfile(UserProfile $profile): static
    {
        $this->profile = $profile;

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
            $accessControl->setGrantedUser($this);
        }

        return $this;
    }

    public function removeAccessControl(AccessControl $accessControl): static
    {
        if ($this->accessControls->removeElement($accessControl)) {
            // set the owning side to null (unless already changed)
            if ($accessControl->getGrantedUser() === $this) {
                $accessControl->setGrantedUser(null);
            }
        }

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
            $propertyRent->setCreatedBy($this);
        }

        return $this;
    }

    public function removePropertyRent(PropertyRent $propertyRent): static
    {
        if ($this->propertyRents->removeElement($propertyRent)) {
            // set the owning side to null (unless already changed)
            if ($propertyRent->getCreatedBy() === $this) {
                $propertyRent->setCreatedBy(null);
            }
        }

        return $this;
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
            $uploadFile->setUpdatedBy($this);
        }

        return $this;
    }

    public function removeUploadFile(UploadFile $uploadFile): static
    {
        if ($this->uploadFiles->removeElement($uploadFile)) {
            // set the owning side to null (unless already changed)
            if ($uploadFile->getUpdatedBy() === $this) {
                $uploadFile->setUpdatedBy(null);
            }
        }

        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    //***** Two factor *******/
/**
     * Return true if the user should do two-factor authentication.
     */
    public function isEmailAuthEnabled(): bool
    {
        return true;
    }

    /**
     * Return user email address.
     */
    public function getEmailAuthRecipient(): string
    {
        return $this->email;
    }

    /**
     * Return the authentication code.
     */
    public function getEmailAuthCode(): string|null
    {
        if(null === $this->authCode){
            throw new \LogicException('The email authentication was not set');            
        }

        return $this->authCode;
    }

    /**
     * Set the authentication code.
     */
    public function setEmailAuthCode(string $authCode): void
    {
        $this->authCode = $authCode;
    }
}
