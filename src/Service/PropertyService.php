<?php

namespace App\Service;

class PropertyService{

    private $dateService;
    public function __construct(DateService $dateService){
        $this->dateService = $dateService;
    }

    /**
     * Retourne le locataire actuel d'un bien immobilier
     * @param mixed $property
     */
    public function getActualTenant($property){
        $actualTenant = null;
        foreach($property->getTenants() as $tenant){
            if($tenant->getRentalStartDate() <= new \DateTime() && $tenant->getRentalEndDate() >= new \DateTime()){
                $actualTenant = $tenant;
            }
        }
        return $actualTenant;
    }

    /**
     * Check si il existe un locataire entre 2 dates données pour une propriétée donnée
     * @param mixed $property
     * @param mixed $startDate
     * @param mixed $endDate
     * @return bool
     */
    public function haveTenantBetweenTwoDates($property, $startDate, $endDate): bool{
        //on récupère les locatires actuel
        if($tenant = $this->getActualTenant($property)){
            //on compare les dates
            if($this->datesCollide($tenant->getRentalStartDate(), $tenant->getRentalEndDate(), $startDate, $endDate)){
                return true;
            }
        };
        return false;
    }

    public function datesCollide(\DateTime $start1, \DateTime $end1, \DateTime $start2, \DateTime $end2): bool {
        return $start1 <= $end2 && $start2 <= $end1;
    }
}