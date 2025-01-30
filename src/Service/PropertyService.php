<?php

namespace App\Service;

class PropertyService{

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
}