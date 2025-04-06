<?php
namespace App\Enum;

enum MortgageBillingPeriodEnum: string {
    case MONTHLY = 'Mensuel';
    case QUARTERLY = 'Trimestrielle ';
    case ANNUAL = 'Annuelle';

}