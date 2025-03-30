<?php

namespace App\Service;

use App\Enum\FinancialCategoryEnum;

class EnumService{
    function mapPropertyRentToFinancialCategory(String $propertyRentType): FinancialCategoryEnum 
    { 
        return match($propertyRentType) { 
            'RENT' => FinancialCategoryEnum::RENT, 
            'PARKING' => FinancialCategoryEnum::PARKING, 
            'CHARGES_DEPOSIT' => FinancialCategoryEnum::CHARGES_DEPOSIT, 
            'FLAT_FEE' => FinancialCategoryEnum::FLAT_FEE,

            'CHARGES_DEPOSIT_OWNER' => FinancialCategoryEnum::CHARGES_DEPOSIT_OWNER, 
        };
    }
}