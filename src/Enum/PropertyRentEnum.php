<?php
namespace App\Enum;

enum PropertyRentEnum: string {
    case RENT = 'Loyer';
    case CHARGES_DEPOSIT = 'Charges (Acompte)';
    case FLAT_FEE = 'Charges (Forfait)';
    case PARKING = 'Parking';

    //dépenses
    case CHARGES_DEPOSIT_OWNER = 'Acompte de charges (Propriétaire)'; 
}