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
    public function getActualTenant($property, $startDate = null){
        $startDate === null ? $date = new \DateTime() : $date = $startDate;
        $actualTenant = null;
        foreach($property->getTenants() as $tenant){
            if($tenant->getRentalStartDate() <= $date && $tenant->getRentalEndDate() >= $date){
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
    public function haveTenantBetweenTwoDates($property, \DateTimeImmutable|\datetime $startDate, \DateTimeImmutable|\datetime $endDate): bool{

        $startDate = $this->dateService->getDateTimeImmutable($startDate);
        $endDate = $this->dateService->getDateTimeImmutable($endDate);
        
        //on récupère les locatires actuel
        if($tenant = $this->getActualTenant($property)){
            //on compare les dates
            if($this->datesCollide($tenant->getRentalStartDate(), $tenant->getRentalEndDate(), $startDate, $endDate)){
                return true;
            }
        };
        return false;
    }

    /**
     * Controle si deux dates ce chevauches
     * @param \DateTime $start1
     * @param \DateTime $end1
     * @param \DateTime $start2
     * @param \DateTime $end2
     * @return bool
     */
    public function datesCollide(\DateTimeImmutable|\datetime $start1, \DateTimeImmutable|\datetime $end1, \DateTimeImmutable|\datetime $start2, \DateTimeImmutable|\datetime $end2): bool {

        //on s'assure du bon format des dates
        $start1 = $this->dateService->getDateTimeImmutable($start1);
        $end1 = $this->dateService->getDateTimeImmutable($end1);
        $start2 = $this->dateService->getDateTimeImmutable($start2);
        $end2 = $this->dateService->getDateTimeImmutable($end2);

        return $start1 <= $end2 && $start2 <= $end1;
    }
}