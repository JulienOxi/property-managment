<?php

namespace App\Controller;

use App\Entity\Lease;
use App\Enum\PropertyRentEnum;
use App\Form\LeaseType;
use App\Entity\PropertyRent;
use App\Enum\AccessRoleEnum;
use App\Service\PropertyService;
use App\Repository\LeaseRepository;
use App\Repository\PropertyRepository;
use App\Repository\UploadFileRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/lease')]
final class LeaseController extends AbstractController
{
    private PropertyService $propertyService;

    public function __construct(PropertyService $propertyService)
    {
        $this->propertyService = $propertyService;
    }
    #[Route(name: 'app_lease_index', methods: ['GET'])]
    public function index(PropertyRepository $propertyRepository, UploadFileRepository $uploadFileRepository, PropertyService $propertyService, CsrfTokenManagerInterface $csrfTokenManager): Response
    {

        // Générer un token pour la génération des entrée financière
        $csrfToken = $csrfTokenManager->getToken('generate_from_property_rent_form')->getValue();

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
            'csrfToken' => $csrfToken
        ]);
    }

    #[Route('/new', name: 'app_lease_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, PropertyService $propertyService, SluggerInterface $slugger): Response
    {
        $lease = new Lease();
        $form = $this->createForm(LeaseType::class, $lease);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //check sur les dates
            if (!$this->dateCheck($lease)) {
                return $this->redirectToRoute('app_lease_new', [$lease], Response::HTTP_SEE_OTHER);
            }

            $lease
                ->setCreatedBy($this->getUser())
                ->setSlug(strtolower($slugger->slug($lease->getProperty()->getName())->toString()));
            $entityManager->persist($lease);
            $entityManager->flush();

            return $this->redirectToRoute('app_lease_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('lease/new.html.twig', [
            'lease' => $lease,
            'form' => $form,
        ]);
    }

    #[Route('/{id}-{slug}', name: 'app_lease_show', methods: ['GET'], requirements: ['id' => Requirement::POSITIVE_INT, 'slug' => Requirement::ASCII_SLUG])]
    public function show(Lease $lease): Response
    {
        return $this->render('lease/show.html.twig', [
            'lease' => $lease,
        ]);
    }

    #[Route('/edit/{id}-{slug}', name: 'app_lease_edit', methods: ['GET', 'POST'], requirements: ['id' => Requirement::POSITIVE_INT, 'slug' => Requirement::ASCII_SLUG])]
    public function edit(Request $request, Lease $lease, EntityManagerInterface $entityManager, PropertyService $propertyService): Response
    {
        $form = $this->createForm(LeaseType::class, $lease);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //check sur les dates
            if (!$this->dateCheck($lease)) {
                return $this->redirectToRoute('app_lease_edit', ['id' => $lease->getId(), 'slug' => $lease->getSlug()], Response::HTTP_SEE_OTHER);
            }


            $entityManager->flush();

            $this->addFlash('success','Modification effectuée');
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
            $this->addFlash('success', 'Le bail à été supprimer.');
        }

        return $this->redirectToRoute('app_lease_index', [], Response::HTTP_SEE_OTHER);
    }


    /**
     * check que la date d'entrée ne correspond pas à une date déjà louée et que la date de sortie est plus grande que la date d'entrée
     * @param \App\Entity\Lease $lease
     * @return bool
     */
    private function dateCheck(Lease $lease)    
    {

        // Détecter si c'est une création (ID null) ou une édition
        $isNew = $lease->getId() === null;

            //check que la date d'entrée ne correspond pas à une date déjà louée
            if($this->propertyService->haveLeaseBetweenTwoDates($lease->getProperty(), $lease->getFromAt(), $lease->getToAt(), $isNew ? [] : $lease))
            {
                $this->addFlash('warning', 'Date de location ce chevauche avec un bail en cours. Veuillez choisir une autre date de location.');
                return false;
            }

            //check que la date de sortie est plus grande que la date d'entrée
            if($lease->getToAt() < $lease->getFromAt())
            {
                $this->addFlash('warning', 'La date de fin de bail doit être plus grande que la date de début.');
                return false;
            }
        
        return true;
    }
}
