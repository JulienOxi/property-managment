<?php

namespace App\Controller;

use App\Entity\Bank;
use App\Form\BankType;
use App\Repository\BankRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

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
            $entityManager->persist($bank);
            $entityManager->flush();

            return $this->redirectToRoute('app_bank_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('bank/new.html.twig', [
            'bank' => $bank,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_bank_show', methods: ['GET'])]
    public function show(Bank $bank): Response
    {
        return $this->render('bank/show.html.twig', [
            'bank' => $bank,
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
