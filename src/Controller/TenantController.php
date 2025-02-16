<?php

namespace App\Controller;

use App\Entity\Tenant;
use App\Entity\Property;
use App\Form\TenantType;
use App\Enum\AccessRoleEnum;
use App\Service\PropertyService;
use App\Service\AccessControlService;
use App\Repository\PropertyRepository;
use App\Repository\UploadFileRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/app/tenant')]
final class TenantController extends AbstractController
{
    private AccessControlService $accessControlService;

    public function __construct(AccessControlService $accessControlService)
    {
        $this->accessControlService = $accessControlService;
    }
    
    #[Route(name: 'app_tenant_index', methods: ['GET'])]
    public function index(PropertyRepository $propertyRepository, UploadFileRepository $uploadFileRepository): Response
    {
        $properties = $propertyRepository->findAccessibleProperties($this->getUser(), [AccessRoleEnum::MEMBER, AccessRoleEnum::OWNER]);

        //on récupère les locataires liés aux propriétés
        $tenants = [];
        foreach ($properties as $property) {
            $tenants = [...$tenants, ...$property->getTenants()];
        }

        //on cherche l'image liée à la propriété pour afficher dans le card header
        $images = [];
        foreach ($tenants as $tenant) {
            $property = $tenant->getProperty();
            $uploadsImages = $uploadFileRepository->findOneBy(['property' => $property->getId(), 'type' => 'image']);
                if (!empty($uploadsImages)) { //on récupère la première immage
                    $images[$tenant->getId()] = $uploadsImages;
                }
            }

        return $this->render('tenant/index.html.twig', [
            'tenants' => $tenants,
            'images' => $images
        ]);
    }

    #[Route('/new', name: 'app_tenant_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, PropertyService $propertyService): Response
    {
        $tenant = new Tenant();
        $form = $this->createForm(TenantType::class, $tenant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //check que la date d'entrée ne correspond pas à une date déjà louée
            if($propertyService->haveTenantBetweenTwoDates($tenant->getProperty(), $tenant->getRentalStartDate(), $tenant->getRentalEndDate()))
            {
                $this->addFlash('warning', 'Date de location ce chevauche avec les locations en cours. Veuillez choisir une autre date de location.');
                return $this->redirectToRoute('app_tenant_new', [$tenant], Response::HTTP_SEE_OTHER);
            }

            $entityManager->persist($tenant);
            $entityManager->flush();

            $this->addFlash('success','Le locataire a bien été crée');

            return $this->redirectToRoute('app_tenant_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('tenant/new.html.twig', [
            'tenant' => $tenant,
            'form' => $form,
            'properties' => $entityManager->getRepository(Property::class)->findAccessibleProperties($this->getUser(), [AccessRoleEnum::MEMBER, AccessRoleEnum::OWNER]),
        ]);
    }

    #[Route('/{id}', name: 'app_tenant_show', methods: ['GET'], requirements: ['id' => Requirement::POSITIVE_INT])]
    public function show(Tenant $tenant): Response
    {
        $propertyCheck = $this->accessControlService->canAccessProperty($this->getUser(), $tenant->getProperty(), AccessRoleEnum::MEMBER);
        if (!$propertyCheck) {
            throw $this->createAccessDeniedException('Vous n’avez pas accès à ces informations.');
        }

        return $this->render('tenant/show.html.twig', [
            'tenant' => $tenant,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_tenant_edit', methods: ['GET', 'POST'], requirements: ['id' => Requirement::POSITIVE_INT])]
    public function edit(Request $request, Tenant $tenant, EntityManagerInterface $entityManager, PropertyService $propertyService): Response
    {
        $propertyCheck = $this->accessControlService->canAccessProperty($this->getUser(), $tenant->getProperty(), AccessRoleEnum::MEMBER);
        if (!$propertyCheck) {
            throw $this->createAccessDeniedException('Vous n’avez pas accès à ces informations.');
        }

        $form = $this->createForm(TenantType::class, $tenant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $entityManager->flush();

            $this->addFlash('success','Modification effectuée');

            return $this->redirectToRoute('app_tenant_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('tenant/edit.html.twig', [
            'tenant' => $tenant,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_tenant_delete', methods: ['POST'], requirements: ['id' => Requirement::POSITIVE_INT])]
    public function delete(Request $request, Tenant $tenant, EntityManagerInterface $entityManager): Response
    {
        $propertyCheck = $this->accessControlService->canAccessProperty($this->getUser(), $tenant->getProperty(), AccessRoleEnum::MEMBER);
        if (!$propertyCheck) {
            throw $this->createAccessDeniedException('Vous n’avez pas accès à ces informations.');
        }

        if ($this->isCsrfTokenValid('delete'.$tenant->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($tenant);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_tenant_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * Route permettant de renouveler la location d'un locataire
     */
    #[Route('/{id}/lease/renew', name: 'app_tenant_lease_renew', methods: ['GET'], requirements: ['id' => Requirement::POSITIVE_INT])]
    public function leaseRenew(Request $request, Tenant $tenant, EntityManagerInterface $entityManager): Response
    {
        $propertyCheck = $this->accessControlService->canAccessProperty($this->getUser(), $tenant->getProperty(), AccessRoleEnum::MEMBER);
        if (!$propertyCheck) {
            throw $this->createAccessDeniedException('Vous n’avez pas accès à ces modifications.');
        }

        $rentTo = $tenant->getRentalEndDate();
        if($rentTo < new \DateTimeImmutable()) {
            //on ajoute 1 an à la date de fin du bail
            $newDate = (new \DateTimeImmutable(date('Y/m/d', $tenant->getRentalEndDate()->getTimestamp())))->modify('+1 year');
            $tenant->setRentalEndDate($newDate);
            $this->addFlash('success', 'La durée du bail a été renouvelée');

            //on boucle pour récupérer tous les types de loyer / charges et modifier la date de fin
            foreach ($tenant->getPropertyRents() as $key => $value) {
                if($value->getEndedAt() < $tenant->getRentalEndDate()) {
                    $value->setEndedAt($tenant->getRentalEndDate());
                }
            }
        }else{
            $this->addFlash('warning', 'Aucun bail à renouveler');
            return $this->redirectToRoute('app_tenant_index', [], Response::HTTP_SEE_OTHER);
        }

        $entityManager->flush();
        $this->addFlash('success', 'Les loyers on été renouvelé pour 1 an');
        return $this->redirectToRoute('app_tenant_index', [], Response::HTTP_SEE_OTHER);
    }
}
