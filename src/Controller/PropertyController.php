<?php

namespace App\Controller;

use App\Entity\Property;
use App\Form\PropertyType;
use App\Repository\FinancialEntryRepository;
use App\Repository\PropertyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/app/property')]
final class PropertyController extends AbstractController
{
    #[Route(name: 'app_property_index', methods: ['GET'])]
    public function index(PropertyRepository $propertyRepository): Response
    {

        return $this->render('property/index.html.twig', [
            'properties' => $propertyRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_property_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $property = new Property();
        $form = $this->createForm(PropertyType::class, $property);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($property);
            $entityManager->flush();

            return $this->redirectToRoute('app_property_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('property/new.html.twig', [
            'property' => $property,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_property_show', methods: ['GET'])]
    public function show(Request $request, Property $property, FinancialEntryRepository $financialEntryRepository): Response
    {

        $year = $request->get('year') ?? date('Y');
        $financialEntrys = $financialEntryRepository->findEntryByPropertyAndYear($property, $year); //selection des loyers (entrées financières)
        $financialDeposit = $financialEntryRepository->findEntryByPropertyAndYear($property, $year, true); //selection des charges (sorties financières)

        //modification de la clef en nom du mois
        $shortFinancialEntrys = [];
        foreach ($financialEntrys as $key => $value) {
            $shortFinancialEntrys[$value->getPaidAt()->format('m').'-'.$value->getCategory()->name] = $value;
        }
        $shortFinancialDeposit = [];
        foreach ($financialDeposit as $key => $value) {
            $shortFinancialDeposit[$value->getPaidAt()->format('m').'-'.$value->getCategory()->name] = $value;
        }

        // selection des hypothèques
        $shortMortgages = [];
        $mortgages = $financialEntryRepository->findMortgageByPropertyAndYear($property, $year);
        foreach ($mortgages as $key => $value) {
            $shortMortgages[$value->getPaidAt()->format('m').'-'.$value->getCategory()->name] = $value;
        }

        return $this->render('property/show.html.twig', [
            'year' => $year,
            'property' => $property,
            'uploads' => null,
            'financialEntrys' => $shortFinancialEntrys,
            'financialDeposit' => $shortFinancialDeposit,
            'mortgages' => $shortMortgages,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_property_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Property $property, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PropertyType::class, $property);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_property_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('property/edit.html.twig', [
            'property' => $property,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_property_delete', methods: ['POST'])]
    public function delete(Request $request, Property $property, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$property->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($property);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_property_index', [], Response::HTTP_SEE_OTHER);
    }
}
