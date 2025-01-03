<?php

namespace App\Controller;

use App\Entity\Property;
use App\Entity\FinancialEntry;
use App\Form\FinancialEntryType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\FinancialEntryRepository;
use App\Repository\PropertyRepository;
use App\Service\DateService;
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

    
    #[Route('/generate/from_property_rent/{property_id}', name: 'app_financial_entry_generate_from_property_rent', methods: ['POST'])]
    public function grnerateFromPropertyRent(FinancialEntryRepository $financialEntryRepository, Request $request, CsrfTokenManagerInterface $csrfTokenManager, EntityManagerInterface $em, DateService $dateService, int $property_id = 0): Response
    {
        //si property_id == 0 on met à jour tous les loyer
        $token = $request->request->get('_csrf_token');

        $property = $em->getRepository(Property::class)->find($property_id);
        if (!$property && $property_id != 0) {
            throw $this->createNotFoundException('Property not found');
        }

        // Vérifier si le token est valide
        if (!$csrfTokenManager->isTokenValid(new \Symfony\Component\Security\Csrf\CsrfToken('generate_from_property_rent_form', $token))) {
            throw $this->createAccessDeniedException('Invalid CSRF token');
        }

        //pour chaque type de loyer dont la date inclus celle d'ajourd'hui
            //On récupère l'entier des jours
                //on regarde si l'entrée existe déjà -> exemple : parking pour le mois de juillet 2024

                $actualRent = []; //contrat de Loyer qui sont d'actualité (entre 2 dates)
                $fullMonth = []; //nombre de mois dans l'intérval
                foreach ($property->getPropertyRents() as $key => $rent) {
                    $dates = $dateService->returnDatesBetweenTwo($rent->getFromAt(), $rent->getEndedAt(), 'Y-m-d');
                    if(in_array(date('Y-m-d'), $dates)){
                        $actualRent[$key] =  $rent;
                        $fullMonth[$key] = $dateService->countFullMonthsBetweenDates($rent->getFromAt(), $rent->getEndedAt());
                    }
                }
                //on récupère les loyer d'actualité pour rechercher les entrées financière
                foreach ($actualRent as $key => $rent) {
                    $financialEntryRepository->findBetweenTwoDates($rent->getFromAt(), $rent->getEndedAt());
                }
                

        $this->addFlash('success', 'Les loyers on correctement été générer !');
        $route = $request->headers->get('referer');

        return $this->redirect($route);
    }
}
