<?php

namespace App\Controller;

use App\Entity\Tenant;
use App\Form\TenantType;
use App\Enum\AccessRoleEnum;
use App\Repository\TenantRepository;
use App\Service\AccessControlService;
use App\Repository\PropertyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
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
    public function index(PropertyRepository $propertyRepository): Response
    {
        $properties = $propertyRepository->findAccessibleProperties($this->getUser(), [AccessRoleEnum::MEMBER, AccessRoleEnum::OWNER]);

        $tenants = [];
        foreach ($properties as $property) {
            $tenants = [...$tenants, ...$property->getTenants()];
        }

        return $this->render('tenant/index.html.twig', [
            'tenants' => $tenants,
        ]);
    }

    #[Route('/new', name: 'app_tenant_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $tenant = new Tenant();
        $form = $this->createForm(TenantType::class, $tenant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($tenant);
            $entityManager->flush();

            $this->addFlash('success','Le locataire a bien été crée');

            return $this->redirectToRoute('app_tenant_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('tenant/new.html.twig', [
            'tenant' => $tenant,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_tenant_show', methods: ['GET'])]
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

    #[Route('/{id}/edit', name: 'app_tenant_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Tenant $tenant, EntityManagerInterface $entityManager): Response
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

    #[Route('/{id}', name: 'app_tenant_delete', methods: ['POST'])]
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

    #[Route('/{id}/lease/renew', name: 'app_tenant_lease_renew', methods: ['GET'])]
    public function leaseRenew(Request $request, Tenant $tenant, EntityManagerInterface $entityManager): Response
    {
        $propertyCheck = $this->accessControlService->canAccessProperty($this->getUser(), $tenant->getProperty(), AccessRoleEnum::MEMBER);
        if (!$propertyCheck) {
            throw $this->createAccessDeniedException('Vous n’avez pas accès à ces modifications.');
        }

        $rentTo = $tenant->getRentalEndDate();
        if($rentTo < new \DateTimeImmutable()) {
            $newDate = (new \DateTimeImmutable(date('Y/m/d', $tenant->getRentalEndDate()->getTimestamp())))->modify('+1 year');
            $tenant->setRentalEndDate($newDate);
            $entityManager->persist($tenant);

            //on boucle pour récupérer tous les types de loyer / charges et modifier la date de fin
            foreach ($tenant->getPropertyRents() as $key => $value) {
                if($value->getEndedAt() < $tenant->getRentalEndDate()) {
                    $value->setEndedAt($tenant->getRentalEndDate());
                    $entityManager->persist($value);
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
