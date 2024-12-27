<?php
namespace App\Enum;

enum AccessRole: string {
    case OWNER = 'Owner';
    case COLLABORATOR = 'Collaborator';
}