<?php

namespace App\Service;

use App\Enum\FinancialCategoryEnum;

class EnumService{
    /**
     * Maps a string to a FinancialCategoryEnum.
     *
     * @param String $name
     * @return FinancialCategoryEnum
     */
    function mapStringToFinancialCategory(String $name): FinancialCategoryEnum 
    { 
        return match($name) { 
            'RENT' => FinancialCategoryEnum::RENT, 
            'PARKING' => FinancialCategoryEnum::PARKING, 
            'CHARGES_DEPOSIT' => FinancialCategoryEnum::CHARGES_DEPOSIT, 
            'FLAT_FEE' => FinancialCategoryEnum::FLAT_FEE,
            'MISCELLANEOUS_INCOME' => FinancialCategoryEnum::MISCELLANEOUS_INCOME,

            'CHARGES_DEPOSIT_OWNER' => FinancialCategoryEnum::CHARGES_DEPOSIT_OWNER, 
        };
    }
}