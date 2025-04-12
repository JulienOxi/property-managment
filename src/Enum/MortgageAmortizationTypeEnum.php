<?php
namespace App\Enum;

enum MortgageAmortizationTypeEnum: string {

    case NOTHING = 'Aucun';
    case DIRECT = 'Direct';
    case INDIRECT = 'Indirect';
    
    }