<?php

namespace App\Service;

use DateTime;

class DateService{

/**
 * Retourne depuis le jours donnée le même jour du mois d'après
 *
 * @param [timestamp] $date
 * @return void
 */
    public function firstAndLastDays($date){
        $day = date('d', $date);
        $month = date('m', $date);
        $year = date('Y', $date);

        $now = new \DateTimeImmutable();
        $fullDate = new \DateTimeImmutable($day . '-' . $month . '-' . $year);
    
        if($now <= $fullDate){
            //si la date de jours et plus petite que la date donnée
            //on dénini le premier jours et le dernier jours de facturation
            $firstDay = $fullDate->sub(new \DateInterval('P1M'));
            $lastDay =  $fullDate;
        }else{
            //si la date de jours et plus grande que la date donnée
            //on dénini le premier jours et le dernier jours de facturation
            $firstDay = $fullDate;
            $lastDay =  $fullDate->add(new \DateInterval('P1M'));
        }

        return ['firstDay' => $firstDay, 'lastDay' => $lastDay];
    }


/**
 * Retourne tous les jours depuis un jour jusque au même jour le mois d'après
 *
 * @param [timestamp] $day Un jours au hasard dans le mois
 * @return array
 */
    public function allDaysFromTheFirst($day):array
    {
        //récupération du premier et du dernier jour du mois
        $firstAndLastDays = $this->firstAndLastDays($day);

        //récupération de tous les jours du mois en fonction du premier et du dernier
        $days = new \DatePeriod($firstAndLastDays['firstDay'], new \DateInterval('P1D'), $firstAndLastDays['lastDay']);
        $allDay = [];
        foreach ($days as $key => $value) {
        array_push($allDay, new \DateTimeImmutable($value->format('Y-m-d')));
        }
        return $allDay;         
    }    


/**
 * Retourne tous les jours entre 2 dates donnée
 *
 * @param [string|timestamp|DateTimeImmutable] $date1
 * @param [string|timestamp|DateTimeImmutable] $date2
 * @param string $format de sortie (default = "d-m-Y")
 * @param boolean $week_end Retourne ou non les jours du week-end, samedi et dimanche (true=avec/false=sans)
 * @return array
 */
function returnDatesBetweenTwo($date1, $date2, $format = 'd-m-Y', $week_end = true ):array {
    $dates = array();
    
    //on formate les dates
    if($date1 instanceof \DateTimeImmutable){
        $date1 = $date1->format('Y-m-d');
    }
    if($date2 instanceof \DateTimeImmutable){
        $date2 = $date2->format('Y-m-d');
    }
    $current = strtotime($date1);
    $date2 = strtotime($date2);
    $stepVal = '+1 day';
    while( $current <= $date2 ) {
      if($week_end == false && !in_array(date('w', $current), [0, 6])){
          $dates[] = date($format, $current);
          $current = strtotime($stepVal, $current);
      }elseif($week_end == true){
          $dates[] = date($format, $current);
          $current = strtotime($stepVal, $current);  
      }else{
          $current = strtotime($stepVal, $current);
      }
    }
    return $dates;
 }    

    function countFullMonthsBetweenDates(\DateTimeImmutable $startDate, \DateTimeImmutable $endDate): array
    {
        // Vérifiez que la date de début est antérieure ou égale à la date de fin
        if ($startDate > $endDate) {
            throw new \InvalidArgumentException('La date de début doit être antérieure ou égale à la date de fin.');
        }
    
        // Clone les dates pour éviter les modifications
        $startOfMonth = $startDate->modify('first day of this month');
        $endOfMonth = $endDate->modify('last day of this month');
    
        $fullMonths = []; // Liste des mois complets trouvés
    
        // Parcourir chaque mois dans l'intervalle
        while ($startOfMonth <= $endOfMonth) {
            $firstDayOfMonth = $startOfMonth->modify('first day of this month');
            $lastDayOfMonth = $startOfMonth->modify('last day of this month');
    
            // Vérifier si le mois est complet dans l'intervalle
            if ($startDate <= $firstDayOfMonth && $endDate >= $lastDayOfMonth) {
                $fullMonths[] = $startOfMonth->format('F Y'); // Ajouter le mois au format "Mois Année"
            }
    
            // Passer au mois suivant
            $startOfMonth = $startOfMonth->modify('first day of next month midnight');
        }
    
        // Retourner le tableau avec les mois et leur nombre
        return [
            'months' => $fullMonths,
            'count' => count($fullMonths),
        ];
    }
}