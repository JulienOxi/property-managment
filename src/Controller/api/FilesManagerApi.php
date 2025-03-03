<?php

namespace App\Controller\api;

use App\Entity\Folder;
use App\Entity\Property;
use App\Entity\UploadFile;
use App\Enum\AccessRoleEnum;
use App\Service\PropertyService;
use Doctrine\ORM\Query\Parameter;
use App\Repository\PropertyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\JsonResponse;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;


#[Route('/api')]
class FilesManagerApi extends AbstractController
{
    private $entityManager ;
    private $helper;
    private $containerBag;

    public function __construct(EntityManagerInterface $entityManager, UploaderHelper $helper, ContainerBagInterface  $containerBag)
    {
        $this->entityManager = $entityManager;
        $this->helper = $helper;
        $this->containerBag = $containerBag;
    }

    #[Route('/files', name: 'api_files_get', methods: ['GET'])]
    public function get(Request $request): JsonResponse
    {
        $folder = $request->query->get('folder');

        return new JsonResponse($this->getAllFiles($folder));
    }

    #[Route('/files', name: 'api_files_post', methods: ['POST'])]
    public function post(Request $request): JsonResponse
    {
        //extensions acceptées
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $documentExtensions = ['txt', 'csv', 'rtf', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'rtf', 'odt', 'ods', 'odp'];

        //on récupère l'extension
        // $extension = $request->files->get('file')->guessExtension();
        $extension = pathinfo($request->files->get('file')->getClientOriginalName(), PATHINFO_EXTENSION);

        // $fileName = pathinfo($request->files->get('file')->getClientOriginalName(), PATHINFO_FILENAME);
        
        $type = null ;
        if(in_array(strtolower($extension), $imageExtensions)){
            $type = 'image';
        }
        if(in_array(strtolower($extension), $documentExtensions)){
            $type = 'document';
        }   

        //on défini le type de fichier
        if($type === null){
            return new JsonResponse([
                "message" => "Ce type de fichier n'est pas autorisé",
            ], 422);
        }

        //recherche du dossier
        $folder = $this->entityManager->getRepository(Folder::class)->createQueryBuilder('f')       
            ->where('f.id = :id')
            ->setParameter('id', $request->request->get('folder'))
            ->getQuery()
            ->getOneOrNullResult();

        if($folder === null){
            return new JsonResponse([
                "message" => "Upload dans le dossier racine interdit.",
            ], 422);
        }

        $uploadFile = new UploadFile();
        $uploadFile
            ->setUpdatedBy($this->getUser())
            ->setProperty($folder->getProperty())
            ->setType($type)
            ->setDescription($request->files->get('file')->getClientOriginalName())
            ->setFolder($folder)
            ->setFile($request->files->get('file'));

        $this->entityManager->persist($uploadFile);
        $this->entityManager->flush();

        $url = $this->getUrlFile($uploadFile);

        return new JsonResponse([
                "id" => $uploadFile->getId(),
                "name" => $uploadFile->getDescription(),
                "url" => $url,
                "size" => $uploadFile->getFileSize(),
                "folder" => $uploadFile->getFolder()->getId(),
                "thumbnail" => $url
            ]);
    }    

    #[Route('/files/{id}', name: 'api_files_delete', methods: ['delete'])]
    public function delete(Request $request, UploadFile $uploadFile, EntityManagerInterface $entityManager): JsonResponse
    {
            if($uploadFile->getUpdatedBy() !== $this->getUser()){
                return new JsonResponse(["message" => "Vous ne pouvez pas supprimer ce fichier"], 403);
            }
            $entityManager->remove($uploadFile);
            $entityManager->flush();

        return new JsonResponse(["message" => "Fichier supprimé"], 204);
    }

    private function getAllFiles($folder) {
        $propertyAccess = $this->entityManager->getRepository(Property::class)->findAccessibleProperties($this->getUser(), [AccessRoleEnum::MEMBER, AccessRoleEnum::OWNER]);

        $allFiles = $this->entityManager->getRepository(UploadFile::class)->createQueryBuilder('u')       
            ->where('u.property IN (:properties)')
            ->andWhere('u.folder = :folder')
            ->setParameters(new ArrayCollection([
                new Parameter('properties', $propertyAccess),
                new Parameter('folder', $folder),
            ]))
            ->getQuery()->getResult();

        $files = [];
        foreach ($allFiles as $file) {
            $url = $this->getUrlFile($file);
            $files[] = [
                "id" => $file->getId(),
                "name" => $file->getDescription(),
                "url" => $url,
                "size" => $file->getFileSize(),
                "folder" => $file->getFolder()->getId(),
                "thumbnail" => $url
            ];
        }

        return $files;
    }


    private function getUrlFile(UploadFile $file) {
        return $_ENV['SERVER_PATH'] .$this->helper->asset($file);
    }


}