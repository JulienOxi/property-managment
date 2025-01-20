<?php 

namespace App\Service;

use App\Entity\User;
use App\Entity\Property;
use App\Enum\AccessRoleEnum;

class AccessControlService
{
    public function canAccessProperty(User $user, Property $property, AccessRoleEnum $requiredRole): bool
    {
        foreach ($property->getAccessControls() as $accessControl) {
            if ($accessControl->getGrantedUser() === $user && $accessControl->getRole()->canAccess($requiredRole)) {
                return true;
            }
        }
    
        return false;
    }
}
