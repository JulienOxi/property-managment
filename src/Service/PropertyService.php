<?php

namespace App\Service;

use App\Entity\Property;

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
        foreach($property->getLeases() as $lease){
            if($lease->getFromAt() <= $date && $lease->getToAt() >= $date){
                $actualTenant = $lease->getTenant();
            }
        }
        return $actualTenant;
    }

    /**
     * Retourne le bail actuel d'un bien immobilier
     * @param mixed $property
     */
    public function getActualLease($property, $startDate = null){
        $startDate === null ? $date = new \DateTime() : $date = $startDate;
        $actuallease= null;
        foreach($property->getLeases() as $lease){
            if($lease->getFromAt() <= $date && $lease->getToAt() >= $date){
                $actuallease = $lease;
            }
        }
        return $actuallease;
    }

    /**
    * Retourne toutes les hypotèque actuelles
    */
    public function getActualMortgages($property, \DateTimeImmutable|\datetime $now = new \DateTime()){
        $actualMortgages = [];
        foreach($property->getMortgages() as $mortgage){
            if($mortgage->getToAt() >= $now && $mortgage->getFromAt() <= $now){
                $actualMortgages[] = $mortgage;
            }
        }
        return $actualMortgages;
    }

    /**
     * Check si il existe un bail entre 2 dates données pour une propriétée donnée
     * @param mixed $property
     * @param mixed $startDate
     * @param mixed $endDate
     * @return bool
     */
    public function haveLeaseBetweenTwoDates($property, \DateTimeImmutable|\datetime $startDate, \DateTimeImmutable|\datetime $endDate, $givenLease = []): bool{

        $startDate = $this->dateService->getDateTimeImmutable($startDate);
        $endDate = $this->dateService->getDateTimeImmutable($endDate);

        //récupère tous les baux liés au bien
        $leases = $property->getLeases();

        foreach ($leases as $lease) {
            //on récupère le bail actuel puis on le compare avec le bail donné
            if($lease != $givenLease){
                //on compare les dates
                if($this->datesCollide($lease->getfromAt(), $lease->getToAt(), $startDate, $endDate)){
                    return true;
                }
            };
        }
        return false;
    }

    /**
     * Controle si deux dates ce chevauches
     * @param \DateTime|\DateTimeImmutable $start1
     * @param \DateTime|\DateTimeImmutable $end1
     * @param \DateTime|\DateTimeImmutable $start2
     * @param \DateTime|\DateTimeImmutable $end2
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