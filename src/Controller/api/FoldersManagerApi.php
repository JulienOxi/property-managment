<?php

namespace App\Controller\api;

use App\Entity\Folder;
use App\Enum\AccessRoleEnum;
use App\Service\PropertyService;
use App\Repository\PropertyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


#[Route('/api')]
class FoldersManagerApi extends AbstractController
{

    private $propertyService;
    private $propertyRepository;
    private $entityManager;
    public function __construct(PropertyService $propertyService, PropertyRepository $propertyRepository, EntityManagerInterface $entityManager)
    {
        $this->propertyService = $propertyService;
        $this->propertyRepository = $propertyRepository;
        $this->entityManager = $entityManager;     
    }

    #[Route('/folders', name: 'api_folders_get', methods: ['GET'])]
    public function index(): JsonResponse
    {
        return new JsonResponse($this->makeFoldersTree());
    }

    #[Route('/folders', name: 'api_folders_post', methods: ['POST'])]
    public function post(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if(!isset($data['parent'])){
            return new JsonResponse(["message" => "Erreur, vous ne pouvez pas créer de dossier ici"], 422);
        }
        $folderParent = $this->entityManager->getRepository(Folder::class)->findOneBy(['id' => $data['parent']]);
        if($folderParent === null){
            return new JsonResponse(["message" => "Erreur, dossier introuvable"], 422);
        }

        $folder = new Folder();
            $folder
                ->setName($data['name'])
                ->setParent($folderParent)
                ->setProperty($folderParent->getProperty());

        $this->entityManager->persist($folder);
        $this->entityManager->flush();
        
        return new JsonResponse([
            'id' => (string) $folder->getId(),
            'name' => $folder->getName(),
            'parent' => $folder->getParent() ? (string) $folder->getParent()->getId() : null,
        ], 201);
    }

    #[Route('/folders/{id}', name: 'api_folders_delete', methods: ['delete'])]
    public function delete(Folder $folder, EntityManagerInterface $entityManager): JsonResponse
    {
            if($folder->getParent() == null){
                return new JsonResponse(["message" => "Vous ne pouvez pas supprimer ce dossier"], 403);
            }
            $entityManager->remove($folder);
            $entityManager->flush();

        return new JsonResponse(["message" => "Dossier supprimé"], 204);
    }

private function makeFoldersTree() {
    $propertyAccess = $this->propertyRepository->findAccessibleProperties($this->getUser(), [AccessRoleEnum::MEMBER, AccessRoleEnum::OWNER]);

    $foldersTree = [];

    foreach ($propertyAccess as $property) {
        $folders = $property->getFolders();

        foreach ($folders as $folder) {

            if($folder->getName() === "Home"){
                $folder->setName($folder->getProperty()->getName());
            }

            $foldersTree[] = [
                'id' => (string) $folder->getId(),
                'name' => $folder->getName(),
                'parent' => $folder->getParent() ? (string) $folder->getParent()->getId() : null,
            ];
        }
    }

    return $foldersTree;
}


}