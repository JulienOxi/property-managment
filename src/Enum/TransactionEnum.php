<?php

namespace App\Enum;

enum TransactionEnum: string {
    case INCOME = 'Entrée';   // Entrée d'argent.
    case EXPENSE = 'Sortie'; // Sortie d'argent.
}