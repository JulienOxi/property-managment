<?php

namespace App\Controller\api;

use App\Entity\Property;
use App\Enum\AccessRoleEnum;
use App\Entity\FinancialEntry;
use App\Service\AccessControlService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


#[Route('/api')]
class StatsApi extends AbstractController
{
    private $entityManager;
    private $accessControlService;

    public function __construct(EntityManagerInterface $entityManager, AccessControlService $accessControlService)
    {
        $this->entityManager = $entityManager;
        $this->accessControlService = $accessControlService;
    }

    #[Route('/count-properties', name: 'api_count_properties', methods: ['GET'])]
    public function post(Request $request): JsonResponse
    {

        return new JsonResponse([
                "nom" => "propriétés",
                "value" => $this->entityManager->getRepository(Property::class)->count([])
            ]);
    }    

    #[Route('/property-rent-year/{id}', name: 'api_property-rent-year', methods: ['GET'])]
    public function propertyRentYear(Property $property, SerializerInterface $serializer): JsonResponse
    {
        $propertyCheck = $this->accessControlService->canAccessProperty($this->getUser(), $property, AccessRoleEnum::MEMBER);
        if (!$propertyCheck) {
            return new JsonResponse(["message" => "Vous n'avez aps accès à cette proprieté"], 403);
        }

        $financialEntrys = $this->entityManager->getRepository(FinancialEntry::class)->findEntryByPropertyAndYear($property, date('Y'), false, true);

        $jsonFinancialEntrys = $serializer->serialize($financialEntrys, 'json', ['groups' => ['public-view']]);
        
        return new JsonResponse([
                "nom" => "Finance",
                "value" => $jsonFinancialEntrys
            ]);
    }    



}