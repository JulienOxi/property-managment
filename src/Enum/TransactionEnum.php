<?php

namespace App\Enum;

enum TransactionEnum: string {
    case INCOME = 'Revenu';   // Entrée d'argent.
    case EXPENSE = 'Dépense'; // Sortie d'argent.


    public static function fromName(string $name): ?self {
        foreach (self::cases() as $case) {
            if ($case->name === $name) {
                return $case;
            }
        }
        return null; // Retourne null si aucun cas ne correspond
    }

}