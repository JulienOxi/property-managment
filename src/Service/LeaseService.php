<?php

namespace App\Service;

use App\Entity\Lease;
use App\Entity\Property;

class LeaseService{

    private $dateService;
    public function __construct(DateService $dateService){
        $this->dateService = $dateService;
    }


        /**
        * Retourne le statut de location actuel du bail
        * - Location à venir : 0 (commence dans plus d’un mois)
        * - Location prochaine : 1 (commence dans moins d’un mois)
        * - Actuellement loué : 2 (entre fromAt et toAt)
        * - Location bientôt terminée : 3 (se termine dans moins de 3 mois)
        * - Location terminée : 4 (toAt est dans le passé)
        *
        * @param \App\Entity\Lease $lease
        * @return array{int, text: string, color: string}
        */
        public function getInfos(Lease $lease): array
        {

            $now = new \DateTimeImmutable();
            $fromAt = $this->dateService->getDateTimeImmutable($lease->getFromAt());
            $toAt = $this->dateService->getDateTimeImmutable($lease->getToAt());

            // Bail encore à venir
            if ($now < $fromAt) {
                $daysUntilStart = $now->diff($fromAt)->days;

                if ($daysUntilStart > 30) {
                    return ['code' => 0, 'statusText' => 'Location à venir', 'statusColor' => 'bg-indigo-200', 'isRented' => false];
                }

                return ['code' => 1, 'statusText' => 'Location prochaine', 'statusColor' => 'bg-teal-200', 'isRented' => false];
            }

            // Bail en cours
            if ($now >= $fromAt && $now <= $toAt) {
                $daysUntilEnd = $now->diff($toAt)->days;

                if ($daysUntilEnd < 90) {
                    return ['code' => 3, 'statusText' => 'Location bientôt terminée', 'statusColor' => 'bg-fuchsia-300', 'isRented' => true];
                }

                return ['code' => 2, 'statusText' => 'Actuellement loué', 'statusColor' => 'bg-green-200', 'isRented' => true];
            }

            // Bail terminé
            return ['code' => 4, 'statusText' => 'Location terminée', 'statusColor' => 'bg-red-500', 'isRented' => false];
        }

}