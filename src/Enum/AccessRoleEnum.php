<?php
namespace App\Enum;

enum AccessRoleEnum: string {
    case OWNER = 'Propriétaire';
    case MEMBER = 'Membre';
    case GUEST = 'Invité';

    public function getPriority(): int
    {
        return match ($this) {
            self::OWNER => 3,
            self::MEMBER => 2,
            self::GUEST => 1,
        };
    }

    public function canAccess(AccessRoleEnum $requiredRole): bool
    {
        return $this->getPriority() >= $requiredRole->getPriority();
    }
}