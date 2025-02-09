<?php

namespace App\Enum;

enum FinancialCategoryEnum: string {
    // Entrées financières
    case RENT = 'Loyer';
    case PARKING = 'Place de parc';
    case CHARGES = 'Charges';
    case MISCELLANEOUS_INCOME = 'Revenu divers';

    // Sorties financières
    case CHARGES_DEPOSIT = 'Acompte de charges'; 
    case WATER = 'Eau';
    case HEATER = 'Chauffage';
    case ELECTRICITY = 'Electricité';
    case WORKS = 'Travaux divers';
    case MORTGAGE = 'Hypothèque';
    case TAXES = 'Impôts';
    case MAINTENANCE = 'Maintenance';
    case INSURANCE = 'Assurance';
    case BANK_FEE = 'Frais bancaire';
    case MISCELLANEOUS_EXPENSE = 'Dépenses diverses';

    // Transferts
    case BANK_TRANSFER = 'Transfert bancaire';

    /**
     * Summary of getByType
     * @param string|int $value
     * @return FinancialCategoryEnum[]
     */
    public static function getByType(string|int $value): string|array {
        $mapping = [
            'INCOME' => [self::RENT, self::PARKING, self::CHARGES, self::MISCELLANEOUS_INCOME],
            'EXPENSE' => [self::CHARGES_DEPOSIT, self::WATER, self::HEATER, self::ELECTRICITY, 
                          self::WORKS, self::MORTGAGE, self::TAXES, self::MAINTENANCE, 
                          self::INSURANCE, self::BANK_FEE, self::BANK_TRANSFER, 
                          self::MISCELLANEOUS_EXPENSE, self::BANK_TRANSFER]
        ];
    
        return $mapping[$value] ?? throw new \InvalidArgumentException("Unknown type: $value");
    }
    

    public static function fromName(string $name): ?self {
        foreach (self::cases() as $case) {
            if ($case->name === $name) {
                return $case;
            }
        }
        return null; // Retourne null si aucun cas ne correspond
    }
}
