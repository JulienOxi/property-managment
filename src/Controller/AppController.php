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
        $properties = $propertyRepository->findAccessibleProperties($this->getUser(), [AccessRoleEnum::MEMBER, AccessRoleEnum::OWNER]);

        $totalRents = 0;
        $totalExpenses = 0;
        $totalUnpaidRents = 0;
        $totalPropertyWithTenant = 0;
        $totalPropertyWithoutTenant = 0;
        $deadlines = []; //array type => bank ou loyer / name => (nom banque ou locataire) / preoperty => property.name
        foreach ($properties as $key => $property) {
            $rent = $entityManager->getRepository(FinancialEntry::class)->findSumAmountBetweenTwoDates($property->getBank(), date('Y-01-01'), date('Y-12-31'), TransactionEnum::INCOME, $property);
            $expenses = $entityManager->getRepository(FinancialEntry::class)->findSumAmountBetweenTwoDates($property->getBank(), date('Y-01-01'), date('Y-12-31'), TransactionEnum::EXPENSE, $property);
            $unpaidRents = $entityManager->getRepository(FinancialEntry::class)->getUnpaidRents($property);

            $property->setTotalRents($rent ? $rent : 0);//ajoute le total des loyers
            $property->setTotalExpenses($expenses ? $expenses : 0); //ajoute le total des dépenses
            $property->setUnpaidRents($unpaidRents ? $unpaidRents : 0); //ajoute le total des loyers impayés
            $totalRents += $rent; //ajoute le total des loyers général
            $totalExpenses += $expenses;    //ajoute le total des dépenses général	
            $totalUnpaidRents += $unpaidRents; //ajoute le total des loyers impayés général

            // Count property with tenant and without tenant
            $property->setActualTenant($propertyService->getActualTenant($property));
            if ($property->getActualTenant()) {
                $totalPropertyWithTenant++;
            } else {
                $totalPropertyWithoutTenant++;
            }

            //ajoute toutes les hypotèques à la liste
            array_push($deadlines,['type' => 'Bancaire', 'name' => $property->getBank()->getName(), 'date' => $property->getMortgageEndDate(), 'property' => $property->getName()]);
            if($property->getActualTenant() != null){
                array_push($deadlines,['type' => 'Bail à loyer', 'name' => $property->getActualTenant()->getFullName(), 'date' => $property->getActualTenant()->getRentalEndDate(), 'property' => $property->getName()]);
            }
            if($property->getMortgageEndDate2() != null){
                array_push($deadlines,['type' => 'Bancaire', 'name' => $property->getBank()->getName(), 'date' => $property->getMortgageEndDate2(), 'property' => $property->getName()]);
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
            'totalPropertyWithTenant' => $totalPropertyWithTenant,
            'totalPropertyWithoutTenant' => $totalPropertyWithoutTenant,
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
