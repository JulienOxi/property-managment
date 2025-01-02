<?php

namespace App\Enum;

enum FinancialCategoryEnum: string {
    // Entrées financières
    case RENT = 'Loyer';
    case PARKING = 'Place de parc';
    case CHARGES = 'charges';
    case MISCELLANEOUS_INCOME = 'Revenu divers';

    // Sorties financières
    case WATER = 'Eau';
    case HEATER = 'Chauffage';
    case MORTAGE = 'Hypotèque';
    case TAXES = 'Impôts';
    case MAINTENANCE = 'maintenance';
    case INSURANCE = 'Assurance';
    case MISCELLANEOUS_EXPENSE = 'Dépenses divers';

    // Transferts
    case BANK_TRANSFER = 'Transfère bancaire';
}
