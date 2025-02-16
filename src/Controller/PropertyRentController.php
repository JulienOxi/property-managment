<?php

namespace App\Controller;

use App\Entity\Property;
use App\Entity\PropertyRent;
use App\Enum\AccessRoleEnum;
use App\Service\DateService;
use App\Form\PropertyRentType;
use App\Service\PropertyService;
use App\Service\AccessControlService;
use App\Repository\PropertyRepository;
use App\Repository\UploadFileRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\PropertyRentRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/app/propertyrent')]
final class PropertyRentController extends AbstractController
{

    private AccessControlService $accessControlService;

    public function __construct(AccessControlService $accessControlService)
    {
        $this->accessControlService = $accessControlService;
    }
    
    #[Route(name: 'app_property_rent_index', methods: ['GET'])]
    public function index(PropertyRentRepository $propertyRentRepository, PropertyRepository $PropertyRepository, UploadFileRepository $uploadFileRepository, DateService $dateService, CsrfTokenManagerInterface $csrfTokenManager): Response
    {
        // Générer un token pour la génération des entrée financière
        $csrfToken = $csrfTokenManager->getToken('generate_from_property_rent_form')->getValue();

        $properties = $PropertyRepository->findAccessibleProperties($this->getUser(), [AccessRoleEnum::MEMBER, AccessRoleEnum::OWNER]);

        //on cherche les images liées aux propriétés pour afficher dans le card header
        $images = [];
        foreach ($properties as $property) {
        $uploadsImages = $uploadFileRepository->findOneBy(['property' => $property->getId(), 'type' => 'image']);
            if (!empty($uploadsImages)) { //on récupère la première immage
                $images[$property->getId()] = $uploadsImages;
            }
        }

        //calcul du loyer total par apparteemnt en fonction des loyer en cours
        $totalRenting = 0;
        foreach ($properties as $property) {
            foreach ($property->getPropertyRents() as $key => $rent) {
                $dates = $dateService->returnDatesBetweenTwo($rent->getFromAt(), $rent->getEndedAt(), 'Y-m-d');
                if(in_array(date('Y-m-d'), $dates)){
                    if ($rent->getType()->name != 'CHARGES_DEPOSIT') { // On ne prend pas en compte les charges
                        $property->setTotalRents($property->getTotalRents() + $rent->getMonthlyPrice());
                    }
                }
            }  
        }

        return $this->render('property_rent/index.html.twig', [
            'property_rents' => $propertyRentRepository->findAll(),
            'properties' => $properties,
            'csrf_token' => $csrfToken,
            'images' => $images
        ]);
    }

    #[Route('/new', name: 'app_property_rent_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, PropertyService $propertyService): Response
    {
        $propertyRent = new PropertyRent();
        $form = $this->createForm(PropertyRentType::class, $propertyRent);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $propertyRent->setTenant($propertyService->getActualTenant($propertyRent->getProperty()));
            $propertyRent->setCreatedBy($this->getUser());
            $entityManager->persist($propertyRent);
            $entityManager->flush();

            $this->addFlash('success','Le loyer à bien été crée');

            return $this->redirectToRoute('app_property_rent_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('property_rent/new.html.twig', [
            'property_rent' => $propertyRent,
            'form' => $form,
            'properties' => $entityManager->getRepository(Property::class)->findAccessibleProperties($this->getUser(), [AccessRoleEnum::MEMBER, AccessRoleEnum::OWNER]),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_property_rent_edit', methods: ['GET', 'POST'], requirements: ['id' => Requirement::POSITIVE_INT])]
    public function edit(Request $request, PropertyRent $propertyRent, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PropertyRentType::class, $propertyRent);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            
            $this->addFlash('success','Modification effectuée');

            return $this->redirectToRoute('app_property_rent_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('property_rent/edit.html.twig', [
            'property_rent' => $propertyRent,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_property_rent_delete', methods: ['POST'], requirements: ['id' => Requirement::POSITIVE_INT])]
    public function delete(Request $request, PropertyRent $propertyRent, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$propertyRent->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($propertyRent);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_property_rent_index', [], Response::HTTP_SEE_OTHER);
    }


    #[Route('/gettenant/{propertyId}', name: 'app_property_rent_get_tenant', methods: ['POST', 'GET'], requirements: ['propertyId' => Requirement::POSITIVE_INT])]
    public function getTenantFromProperty($propertyId, PropertyService $propertyService, PropertyRepository $propertyRepository): JsonResponse
    {
        $tenant = $propertyService->getActualTenant($propertyRepository->findOneBy(['id' => $propertyId]));
        if($tenant == null){
            return new JsonResponse([
                'error' => 'tenant is null',
                'message' => 'Aucun locataire trouvé pour ce bien immobilier.'
            ]
            );
        }

        return new JsonResponse([
            'success' => true,
            'message' => $tenant->getFullName()
        ]);
    }
}
