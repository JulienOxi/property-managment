<?php
namespace App\Enum;

enum PropertyRentEnum: string {
    case RENT = 'Loyer';
    case CHARGES = 'Charges';
    case PARKING = 'Parking';

    //dépenses
    case CHARGES_DEPOSIT = 'Acompte de charges'; 
}