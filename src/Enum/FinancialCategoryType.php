<?php

namespace App\Enum;

enum FinancialCategoryType: string {
    // Entrées financières
    case RENT = 'rent';                     // Loyer
    case PARKING = 'parking';               // Revenus des places de parc
    case CHARGES = 'charges';               // Charges de location
    case MISCELLANEOUS_INCOME = 'misc_income'; // Revenus divers

    // Sorties financières
    case UTILITIES = 'utilities';           // Charges (eau, électricité, chauffage)
    case MORTAGE = 'mortgage';             // Remboursement hypothécaire
    case TAXES = 'taxes';                   // Impôts
    case MAINTENANCE = 'maintenance';       // Entretien/réparations
    case INSURANCE = 'insurance';           // Assurances
    case MISCELLANEOUS_EXPENSE = 'misc_expense'; // Dépenses diverses

    // Transferts
    case BANK_TRANSFER = 'bank_transfer';    // Transfert entre comptes bancaires
}