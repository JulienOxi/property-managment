<?php

namespace App\Service;

use App\Entity\FinancialEntry;

class MortgageService{

    private $dateService;
    public function __construct(DateService $dateService){
        $this->dateService = $dateService;
    }


    /**
    * Valide une entrée financière de type hypothèque en comparant la date de paiement à une date spécifique pour les hypohèques
    * @param \App\Entity\Mortgage $mortgage
    * @param \App\Entity\FinancialEntry $financialEntry
    * @return bool
     */
    public function isPaiementDateValid(FinancialEntry $financialEntry): bool
    {
        if (!$financialEntry->getMortgage()) {
            return false; // Pas de lien avec une hypothèque
        }


        $paiementDate = [];
        if($financialEntry->getMortgage()->getBillingPeriod()->name === 'QUARTERLY'){
            $paiementDate = ['-03-31', '-06-30', '-09-30', '-12-31'];
        }

        if($financialEntry->getMortgage()->getBillingPeriod()->name === 'MONTHLY'){
            $paiementDate = ['-01-31', '-02-28', '-03-31', '-04-30', '-05-31', '-06-30', '-07-31', '-08-31', '-09-30', '-10-31', '-11-30', '-12-31'];
        }
        if($financialEntry->getMortgage()->getBillingPeriod()->name === 'YEARLY'){
            $paiementDate = ['-12-31'];
        }
        return in_array(date('-m-d', $financialEntry->getPaidAt()->getTimestamp()), $paiementDate);
    
    }

}