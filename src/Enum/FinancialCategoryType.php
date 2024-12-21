<?php

namespace App\Enum;

enum FinancialCategoryType: string {
    // Entrées financières
    case Rent = 'rent';                     // Loyer
    case Parking = 'parking';               // Revenus des places de parc
    case Charges = 'charges';               // Charges de location
    case MiscellaneousIncome = 'misc_income'; // Revenus divers

    // Sorties financières
    case Utilities = 'utilities';           // Charges (eau, électricité, chauffage)
    case Mortgage = 'mortgage';             // Remboursement hypothécaire
    case Taxes = 'taxes';                   // Impôts
    case Maintenance = 'maintenance';       // Entretien/réparations
    case Insurance = 'insurance';           // Assurances
    case MiscellaneousExpense = 'misc_expense'; // Dépenses diverses

    // Transferts
    case BankTransfer = 'bank_transfer';    // Transfert entre comptes bancaires
}