<?php

namespace App\Service;

use DateTime;
use App\Enum\PropertyRentEnum;
use App\Enum\FinancialCategoryEnum;

class EnumService{
    function mapPropertyRentToFinancialCategory(String $propertyRentType): FinancialCategoryEnum 
    { 
        return match($propertyRentType) { 
            'RENT' => FinancialCategoryEnum::RENT, 
            'PARKING' => FinancialCategoryEnum::PARKING, 
            'CHARGES' => FinancialCategoryEnum::CHARGES, 
        };
    }
}