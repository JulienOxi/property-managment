<?php

namespace App\Controller;

use App\Entity\Bank;
use App\Form\BankType;
use App\Enum\TransactionEnum;
use App\Repository\BankRepository;
use Symfony\UX\Chartjs\Model\Chart;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\FinancialEntryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/bank')]
final class BankController extends AbstractController
{
    #[Route(name: 'app_bank_index', methods: ['GET'])]
    public function index(BankRepository $bankRepository): Response
    {
        return $this->render('bank/index.html.twig', [
            'banks' => $bankRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_bank_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $bank = new Bank();
        $form = $this->createForm(BankType::class, $bank);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $bank->setCreatedBy($this->getUser());
            
            $entityManager->persist($bank);
            $entityManager->flush();

            return $this->redirectToRoute('app_bank_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('bank/new.html.twig', [
            'bank' => $bank,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_bank_show', methods: ['GET', 'POST'])]
    public function show(Bank $bank, Request $request, FinancialEntryRepository $financialEntryRepository, ChartBuilderInterface $chartBuilder): Response
    {
        $chart = null;
        $form = $this->createFormBuilder()
            ->add('dateFrom', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date du',
                'required' => true,
            ])
            ->add('dateTo', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date au',
                'required' => true,
            ])
            ->getForm();

        $form->handleRequest($request);
        
        $sortAmountIncome = null;
        $sortAmountExpense = null;
        $sortTotalAmount = null;
        
        if ($form->isSubmitted() && $form->isValid()) {
            $dateFrom = $form->get('dateFrom')->getData();
            $dateTo = $form->get('dateTo')->getData();
            $sortAmountIncome = $financialEntryRepository->findSumAmountBetweenTwoDates($bank, $dateFrom, $dateTo, TransactionEnum::INCOME);
            $sortAmountExpense = $financialEntryRepository->findSumAmountBetweenTwoDates($bank, $dateFrom, $dateTo, TransactionEnum::EXPENSE);            
            $sortTotalAmount = $sortAmountIncome - $sortAmountExpense;

            //création du graphique
            $chart = $chartBuilder->createChart(Chart::TYPE_BAR);

            $chart->setData([
                'labels' => [number_format($sortAmountIncome, 2, '.', '\''), number_format($sortAmountExpense, 2, '.', '\''), number_format($sortTotalAmount, 2, '.', '\'')],//[$sortAmountIncome, $sortAmountExpense, $sortTotalAmount],
                'datasets' => [
                    [
                        'label' => 'Dépenses - Revenus - Gains',
                        'backgroundColor' => ['rgb(8, 241, 8)', 'rgb(255, 99, 172)', 'rgb(111, 0, 255)'],
                        'borderColor' => ['rgb(1, 141, 1)', 'rgb(139, 50, 69)', 'rgb(69, 0, 160)'],
                        'data' => [$sortAmountIncome, $sortAmountExpense, $sortTotalAmount]
                    ]
                ]
            ]);

            $chart->setOptions([
                'scales' => [
                    'yAxes' => [
                        [
                            'ticks' => [
                                'beginAtZero' => true
                            ]
                        ]
                    ]
                ]
            ]);

        }else{
            $dateFrom = new \DateTime('2000-01-01');
            $dateTo = new \DateTime('last day of this month');
        }
        
        $totalAmountIncome = $financialEntryRepository->findSumAmountBetweenTwoDates($bank, new \DateTime('2000-01-01'), new \DateTime('last day of this month'), TransactionEnum::INCOME);
        $totalAmountExpense = $financialEntryRepository->findSumAmountBetweenTwoDates($bank, new \DateTime('2000-01-01'), new \DateTime('last day of this month'), TransactionEnum::EXPENSE);
        $totalAmount = $totalAmountIncome - $totalAmountExpense;

        return $this->render('bank/show.html.twig', [
            'bank' => $bank,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'totaLAmount' => $totalAmount,
            'totalAmountIncome' => $totalAmountIncome,
            'totalAmountExpense' => $totalAmountExpense,
            'sortAmountIncome' => $sortAmountIncome,
            'sortAmountExpense' => $sortAmountExpense,
            'sortTotalAmount' => $sortTotalAmount,
            'form' => $form->createView(),
            'chart' => $chart,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_bank_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Bank $bank, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(BankType::class, $bank);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_bank_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('bank/edit.html.twig', [
            'bank' => $bank,
            'form' => $form,
        ]);
    }
}
