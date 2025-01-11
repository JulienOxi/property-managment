<?php

namespace App\Enum;

enum FinancialCategoryEnum: string {
    // Entrées financières
    case RENT = 'Loyer';
    case PARKING = 'Place de parc';
    case CHARGES = 'charges';
    case MISCELLANEOUS_INCOME = 'Revenu divers';

    // Sorties financières
    case CHARGES_DEPOSIT = 'Acompte de charges'; 
    case WATER = 'Eau';
    case HEATER = 'Chauffage';
    case ELECTRICITY = 'Electricité';
    case WORKS = 'Travaux divers';
    case MORTAGE = 'Hypotèque';
    case TAXES = 'Impôts';
    case MAINTENANCE = 'maintenance';
    case INSURANCE = 'Assurance';
    case BANK_FEE = 'Frais bancaire';

    case MISCELLANEOUS_EXPENSE = 'Dépenses divers';

    // Transferts
    case BANK_TRANSFER = 'Transfère bancaire';

    public static function fromName(string $name): ?self {
        foreach (self::cases() as $case) {
            if ($case->name === $name) {
                return $case;
            }
        }
        return null; // Retourne null si aucun cas ne correspond
    }
}
