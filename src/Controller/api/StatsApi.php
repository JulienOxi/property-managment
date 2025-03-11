<?php

namespace App\Controller\api;

use App\Entity\Property;
use App\Enum\AccessRoleEnum;
use App\Entity\FinancialEntry;
use App\Service\AccessControlService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


#[Route('/api')]
class StatsApi extends AbstractController
{
    private $entityManager;
    private $accessControlService;

    public function __construct(EntityManagerInterface $entityManager, AccessControlService $accessControlService)
    {
        $this->entityManager = $entityManager;
        $this->accessControlService = $accessControlService;
    }


    #[Route('/properties-evolution', name: 'api_properties_evolution', methods: ['GET'])]
    public function propertiesEvolution(SerializerInterface $serializer): JsonResponse
    {


        $properties = $this->entityManager->getRepository(Property::class)->findAccessibleProperties($this->getUser(), AccessRoleEnum::OWNER, true);

        $incomeAll = [];
        $expensesAll = [];
        foreach ($properties as $key => $property) {
            $financialEntrysIncome = $this->entityManager->getRepository(FinancialEntry::class)->findEntryByPropertyAndYear($property, date('Y'), false);
            $financialEntrysExpenses = $this->entityManager->getRepository(FinancialEntry::class)->findEntryByPropertyAndYear($property, date('Y'), true);

            $incomeAll = array_merge($incomeAll, $financialEntrysIncome);
            $expensesAll = array_merge($expensesAll, $financialEntrysExpenses);
        }

            $incomeByMonth = array_fill(1, 12, 0);
            $expensesByMonth = array_fill(1, 12, 0);

            foreach ($incomeAll as $income) {
                $month = (int) $income->getpaidAt()->format('n'); // Extrait le mois (1-12)
                $incomeByMonth[$month] += $income->getAmount(); // Additionne les montants
            }

            foreach ($expensesAll as $expense) {
                $month = (int) $expense->getpaidAt()->format('n');
                $expensesByMonth[$month] += $expense->getAmount();
            }

            // Passer les données au template
            return new JsonResponse([
                'incomeByMonth' => json_encode(array_values($incomeByMonth)), 
                'expensesByMonth' => json_encode(array_values($expensesByMonth))
            ]);
    }    



}