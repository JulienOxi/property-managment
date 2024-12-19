<?php
namespace App\Enum;

enum AccessRole: string {
    case Owner = 'Owner';
    case Collaborator = 'Collaborator';
}