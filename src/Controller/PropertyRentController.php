<?php

namespace App\Controller;

use App\Entity\PropertyRent;
use App\Service\DateService;
use App\Form\PropertyRentType;
use App\Repository\PropertyRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\PropertyRentRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/app/propertyrent')]
final class PropertyRentController extends AbstractController
{
    #[Route(name: 'app_property_rent_index', methods: ['GET'])]
    public function index(PropertyRentRepository $propertyRentRepository, PropertyRepository $PropertyRepository, DateService $dateService, CsrfTokenManagerInterface $csrfTokenManager): Response
    {
        // Générer un token pour la génération des entrée financière
        $csrfToken = $csrfTokenManager->getToken('generate_from_property_rent_form')->getValue();

        $propertys = $PropertyRepository->findAll();

        //calcul du loyer total par apparteemnt en fonction des loyer en cours
        $totalRenting = 0;
        foreach ($propertys as $property) {
            foreach ($property->getPropertyRents() as $key => $rent) {
                $dates = $dateService->returnDatesBetweenTwo($rent->getFromAt(), $rent->getEndedAt(), 'Y-m-d');
                if(in_array(date('Y-m-d'), $dates)){
                    $property->setTotalRents($property->getTotalRents() + $rent->getMonthlyPrice());
                }
            }  
        }

        return $this->render('property_rent/index.html.twig', [
            'property_rents' => $propertyRentRepository->findAll(),
            'propertys' => $propertys,
            'csrf_token' => $csrfToken,
        ]);
    }

    #[Route('/new', name: 'app_property_rent_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $propertyRent = new PropertyRent();
        $form = $this->createForm(PropertyRentType::class, $propertyRent);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $propertyRent->setCreatedBy($this->getUser());
            $entityManager->persist($propertyRent);
            $entityManager->flush();

            return $this->redirectToRoute('app_property_rent_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('property_rent/new.html.twig', [
            'property_rent' => $propertyRent,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_property_rent_show', methods: ['GET'])]
    public function show(PropertyRent $propertyRent): Response
    {
        return $this->render('property_rent/show.html.twig', [
            'property_rent' => $propertyRent,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_property_rent_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, PropertyRent $propertyRent, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PropertyRentType::class, $propertyRent);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_property_rent_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('property_rent/edit.html.twig', [
            'property_rent' => $propertyRent,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_property_rent_delete', methods: ['POST'])]
    public function delete(Request $request, PropertyRent $propertyRent, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$propertyRent->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($propertyRent);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_property_rent_index', [], Response::HTTP_SEE_OTHER);
    }
}
