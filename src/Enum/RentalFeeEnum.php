<?php
namespace App\Enum;

enum RentalFeeEnum: string {
    case CHARGES_DEPOSIT = 'Charges (Acompte)';
    case FLAT_FEE = 'Charges (Forfait)';
}