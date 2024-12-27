<?php

namespace App\Enum;

enum TransactionType: string {
    case INCOME = 'income';   // Entrée d'argent.
    case EXPENSE = 'expense'; // Sortie d'argent.
}