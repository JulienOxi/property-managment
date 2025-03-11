<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Property;
use App\Enum\AccessRoleEnum;
use Doctrine\ORM\QueryBuilder;
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


    /**
     * @param User $user
     * @param AccessRoleEnum|array $requiredRoles
     * @param bool $result
     * @return Property[]|QueryBuilder
     */
    public function findAccessibleProperties(User $user, AccessRoleEnum|array $requiredRoles, $result = true): array | QueryBuilder
    {
        // Normaliser les rôles en un tableau
        $roles = is_array($requiredRoles) 
            ? array_merge(...array_map([$this, 'getRolesHierarchy'], $requiredRoles)) 
            : $this->getRolesHierarchy($requiredRoles);
    
                
        $query = $this->createQueryBuilder('p')
            ->join('p.accessControls', 'ac')
            ->where('ac.grantedUser = :user')
            ->andWhere('ac.role IN (:roles)')
            ->setParameter('user', $user)
            ->setParameter('roles', $roles);

            if ($result) {
                return $query->getQuery()->getResult();
            }
            
            return $query;
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
