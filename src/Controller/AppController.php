<?php

namespace App\Controller;

use App\Enum\AccessRoleEnum;
use App\Entity\FinancialEntry;
use App\Enum\TransactionEnum;
use App\Repository\UserRepository;
use App\Service\PropertyService;
use Symfony\UX\Chartjs\Model\Chart;
use App\Repository\PropertyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Notifier\TexterInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AppController extends AbstractController
{
    public function __construct(
        private TexterInterface $texter,
    ) {}

    #[Route('/', name: 'app_home')]
    public function app(PropertyRepository $propertyRepository, EntityManagerInterface $entityManager, PropertyService $propertyService): Response
    {
        // Récupère les propriétés accessibles par l'utilisateur
        $properties = $propertyRepository->findAccessibleProperties($this->getUser(), [AccessRoleEnum::MEMBER, AccessRoleEnum::OWNER]);

        // création des variables pour les stats
        $totalRents = 0;
        $totalExpenses = 0;
        $totalUnpaidRents = 0;
        $totalPropertyWithActiveLease = 0;
        $totalPropertyWithoutActiveLease = 0;
        $deadlines = []; //array type => bank ou loyer / name => (nom banque ou locataire) / preoperty => property.name
        foreach ($properties as $key => $property) {
            $rent = $entityManager->getRepository(FinancialEntry::class)->findSumAmountBetweenTwoDates( date('Y-01-01'), date('Y-12-31'), TransactionEnum::INCOME, null, $property);
            $expenses = $entityManager->getRepository(FinancialEntry::class)->findSumAmountBetweenTwoDates(date('Y-01-01'), date('Y-12-31'), TransactionEnum::EXPENSE, null, $property);
            $unpaidRents = $entityManager->getRepository(FinancialEntry::class)->getUnpaidRents($property);

            $property->setTotalRents($rent ? $rent : 0);//ajoute le total des loyers
            $property->setTotalExpenses($expenses ? $expenses : 0); //ajoute le total des dépenses
            $property->setUnpaidRents($unpaidRents ? $unpaidRents : 0); //ajoute le total des loyers impayés
            $totalRents += $rent; //ajoute le total des loyers général
            $totalExpenses += $expenses;    //ajoute le total des dépenses général	
            $totalUnpaidRents += $unpaidRents; //ajoute le total des loyers impayés général

            // Compte le nombre de propriétés avec et sans locataire
            $property->setActualLease($propertyService->getActualLease($property));
            if ($property->getActualLease()) {
                $totalPropertyWithActiveLease++;
            } else {
                $totalPropertyWithoutActiveLease++;
            }

            //ajoute toutes les hypotèques à la liste
            foreach ($property->getMortgages() as $mortgage) {
                if($mortgage->getToAt() >= new \DateTime()){
                    array_push($deadlines,['type' => 'Hypotèque', 'name' => $mortgage->getBank()->getName(), 'date' => $mortgage->getToAt(), 'property' => $mortgage->getProperty()->getName()]);
                }
            }
            if($property->getActualLease() != null){
                array_push($deadlines,['type' => 'Bail à loyer', 'name' => '', 'date' => $property->getActualLease()->getToAt(), 'property' => $property->getName()]);
            }
            // Fonction de comparaison pour trier par date décroissante
            usort($deadlines, function($a, $b) {
                return $a['date'] <=> $b['date'];
            });
        }


        return $this->render('app/index.html.twig', [
            'properties' => $properties,
            'totalRents' => $totalRents,
            'totalExpenses' => $totalExpenses,
            'totalUnpaidRents' => $totalUnpaidRents,
            'totalPropertyWithActiveLease' => $totalPropertyWithActiveLease,
            'totalPropertyWithoutActiveLease' => $totalPropertyWithoutActiveLease,
            'deadlines' => $deadlines
        ]);
    }

    #[Route('/documentation', name: 'app_doc')]
    public function documentation(): Response
    {
        
        return $this->render('documentation.html.twig', [

        ]);
    }
}
