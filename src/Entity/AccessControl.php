<?php

namespace App\Entity;

use App\Enum\AccessRole;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\AccessControlRepository;

#[ORM\Entity(repositoryClass: AccessControlRepository::class)]
class AccessControl
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'accessControls')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $grantedUser = null;

    #[ORM\Column(length: 50)]
    private AccessRole $role = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGrantedUser(): ?User
    {
        return $this->grantedUser;
    }

    public function setGrantedUser(?User $grantedUser): static
    {
        $this->grantedUser = $grantedUser;

        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(AccessRole $role): static
    {
        $this->role = $role;

        return $this;
    }
}
