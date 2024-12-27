<?php
namespace App\Enum;

enum PropertyRentType: string {
    case RENT = 'Loyer';
    case CHARGES = 'Charges';
    case PARKING = 'Parking';
}