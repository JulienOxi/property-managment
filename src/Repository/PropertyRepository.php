<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Property;
use App\Enum\AccessRoleEnum;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Property>
 */
class PropertyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Property::class);
    }


    public function findAccessibleProperties(User $user, AccessRoleEnum $requiredRole): array
{
    return $this->createQueryBuilder('p')
        ->join('p.accessControls', 'ac')
        ->where('ac.grantedUser = :user')
        ->andWhere('ac.role IN (:roles)')
        ->setParameter('user', $user)
        ->setParameter('roles', $this->getRolesHierarchy($requiredRole))
        ->getQuery()
        ->getResult();
}

private function getRolesHierarchy(AccessRoleEnum $requiredRole): array
{
    // Define the hierarchy of roles
    return match ($requiredRole) {
        AccessRoleEnum::GUEST => [AccessRoleEnum::GUEST->value],
        AccessRoleEnum::MEMBER => [AccessRoleEnum::MEMBER->value, AccessRoleEnum::GUEST->value],
        AccessRoleEnum::OWNER => [AccessRoleEnum::OWNER->value, AccessRoleEnum::MEMBER->value, AccessRoleEnum::GUEST->value],
    };
}
}
