<?php
namespace App\Enum;

enum PropertyRentType: string {
    case rent = 'Loyer';
    case charges = 'Charges';
    case parking = 'Parking';
}