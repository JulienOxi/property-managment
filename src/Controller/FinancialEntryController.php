<?php

namespace App\Controller;

use App\Entity\FinancialEntry;
use App\Form\FinancialEntryType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\FinancialEntryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/app/financialentry')]
final class FinancialEntryController extends AbstractController
{
    #[Route(name: 'app_financial_entry_index', methods: ['GET'])]
    public function index(FinancialEntryRepository $financialEntryRepository, CsrfTokenManagerInterface $csrfTokenManager): Response
    {
        // Générer un token pour la génération des entrée financière
        $csrfToken = $csrfTokenManager->getToken('generate_from_property_rent_form')->getValue();

        return $this->render('financial_entry/index.html.twig', [
            'financial_entries' => $financialEntryRepository->findAll(),
            'csrf_token' => $csrfToken,
        ]);
    }

    #[Route('/new', name: 'app_financial_entry_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $financialEntry = new FinancialEntry();
        $form = $this->createForm(FinancialEntryType::class, $financialEntry);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $financialEntry->setCreatedBy($this->getUser());
            $entityManager->persist($financialEntry);
            $entityManager->flush();

            return $this->redirectToRoute('app_financial_entry_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('financial_entry/new.html.twig', [
            'financial_entry' => $financialEntry,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_financial_entry_show', methods: ['GET'])]
    public function show(FinancialEntry $financialEntry): Response
    {
        return $this->render('financial_entry/show.html.twig', [
            'financial_entry' => $financialEntry,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_financial_entry_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, FinancialEntry $financialEntry, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(FinancialEntryType::class, $financialEntry);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_financial_entry_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('financial_entry/edit.html.twig', [
            'financial_entry' => $financialEntry,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_financial_entry_delete', methods: ['POST'])]
    public function delete(Request $request, FinancialEntry $financialEntry, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$financialEntry->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($financialEntry);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_financial_entry_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/generate/from_property_rent', name: 'app_financial_entry_generate_from_property_rent', methods: ['POST'])]
    public function grnerateFromPropertyRent(Request $request, CsrfTokenManagerInterface $csrfTokenManager): Response
    {
        $token = $request->request->get('_csrf_token');

        // Vérifier si le token est valide
        if (!$csrfTokenManager->isTokenValid(new \Symfony\Component\Security\Csrf\CsrfToken('generate_from_property_rent_form', $token))) {
            throw $this->createAccessDeniedException('Invalid CSRF token');
        }
        // Logique si le token est valide
        $this->addFlash('success', 'Form submitted successfully!');
        return $this->redirectToRoute('app_financial_entry_index', [], Response::HTTP_SEE_OTHER);
    }
}
