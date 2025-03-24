<?php

namespace App\Controller;

use App\Entity\Lease;
use App\Form\LeaseType;
use App\Enum\AccessRoleEnum;
use App\Service\PropertyService;
use App\Repository\LeaseRepository;
use App\Repository\PropertyRepository;
use App\Repository\UploadFileRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/lease')]
final class LeaseController extends AbstractController
{
    #[Route(name: 'app_lease_index', methods: ['GET'])]
    public function index(PropertyRepository $propertyRepository, UploadFileRepository $uploadFileRepository, PropertyService $propertyService): Response
    {

        $properties = $propertyRepository->findAccessibleProperties($this->getUser(), [AccessRoleEnum::MEMBER, AccessRoleEnum::OWNER]);
        //on cherche les images liées aux propriétés pour afficher dans le card header
        $images = [];

        foreach ($properties as $property) {
        $uploadsImages = $uploadFileRepository->findBy(['property' => $property->getId(), 'type' => 'image']);
            if (!empty($uploadsImages)) { //on récupère la première immage
                $images[$property->getId()] = array_values($uploadsImages)[0];
            }
            $property->setActualLease($propertyService->getActualLease($property));
        }

        //on récupère les baux liés aux propriétés
        $leases = [];
        foreach ($properties as $property) {
            $leases = [...$leases, ...$property->getLeases()];
        }

        return $this->render('lease/index.html.twig', [
            'leases' => $leases,
            'images' => $images,
        ]);
    }

    #[Route('/new', name: 'app_lease_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, PropertyService $propertyService): Response
    {
        $lease = new Lease();
        $form = $this->createForm(LeaseType::class, $lease);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //check que la date d'entrée ne correspond pas à une date déjà louée
            if($propertyService->haveLeaseBetweenTwoDates($lease->getProperty(), $lease->getFromAt(), $lease->getToAt()))
            {
                $this->addFlash('warning', 'Date de location ce chevauche avec un bail en cours. Veuillez choisir une autre date de location.');
                return $this->redirectToRoute('app_lease_new', [$lease], Response::HTTP_SEE_OTHER);
            }

            $lease->setCreatedBy($this->getUser());
            $entityManager->persist($lease);
            $entityManager->flush();

            return $this->redirectToRoute('app_lease_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('lease/new.html.twig', [
            'lease' => $lease,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_lease_show', methods: ['GET'])]
    public function show(Lease $lease): Response
    {
        return $this->render('lease/show.html.twig', [
            'lease' => $lease,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_lease_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Lease $lease, EntityManagerInterface $entityManager, PropertyService $propertyService): Response
    {
        $form = $this->createForm(LeaseType::class, $lease);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //check que la date d'entrée ne correspond pas à une date déjà louée
            if($propertyService->haveLeaseBetweenTwoDates($lease->getProperty(), $lease->getFromAt(), $lease->getToAt()))
            {
                $this->addFlash('warning', 'Date de location ce chevauche avec un bail en cours. Veuillez choisir une autre date de location.');
                return $this->redirectToRoute('app_lease_edit', ['id' => $lease->getId(), $lease], Response::HTTP_SEE_OTHER);
            }
            $entityManager->flush();

            return $this->redirectToRoute('app_lease_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('lease/edit.html.twig', [
            'lease' => $lease,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_lease_delete', methods: ['POST'])]
    public function delete(Request $request, Lease $lease, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$lease->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($lease);
            $entityManager->flush();
            $this->addFlash('success', 'Bail bien supprimer.');
        }

        return $this->redirectToRoute('app_lease_index', [], Response::HTTP_SEE_OTHER);
    }
}
