<?php

namespace App\Enum;

enum TransactionType: string {
    case Income = 'income';   // Entrée d'argent.
    case Expense = 'expense'; // Sortie d'argent.
}