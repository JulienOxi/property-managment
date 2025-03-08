<?php

namespace App\Controller;

use App\Entity\Folder;
use App\Entity\Property;
use App\Entity\UploadFile;
use App\Enum\AccessRoleEnum;
use App\Form\UploadFileType;
use App\Entity\UploadFileRepository;
use App\Service\AccessControlService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/upload-file')]
#[Route('/', name: 'app_upload_file_index', methods: ['GET', 'POST'])]
final class UploadFileController extends AbstractController
{

    private AccessControlService $accessControlService;
    private EntityManagerInterface $entityManager;

    public function __construct(AccessControlService $accessControlService, EntityManagerInterface $entityManager)
    {
        $this->accessControlService = $accessControlService;
        $this->entityManager = $entityManager;
    }
    
    #[Route('/new/{id}', name: 'app_upload_file_new', methods: ['GET', 'POST'], requirements: ['id' => Requirement::POSITIVE_INT])]
    public function new(Property $id, Request $request): Response
    {
            //on récupère le type de fichier (image ou document)
            $type = $request->query->get('type');
            if($type != 'image' && $type!= 'document'){
                throw new \LogicException('Type non pris en charge');
            }

        $uploadFile = new UploadFile();
        $form = $this->createForm(UploadFileType::class, $uploadFile, [
            'extra_data' => ['type' => $type]//on passe les options au formulaire
        ]);

        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
                
            //on verifie que l'extension est bonne
            $extension = $request->files->get('upload_file')['file']->guessExtension();
            //si le type est image, on verifie que l'extension est bonne
            if ($type === 'image' && !in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                throw new \LogicException('Extension non prise en charge pour les images');
            }            
            
            $uploadFile
                ->setUpdatedBy($this->getUser())
                ->setProperty($id)
                ->setFolder($this->entityManager->getRepository(Folder::class)->findOneBy(['property' => $id, 'name' => "Home"]));

            $this->entityManager->persist($uploadFile);
            $this->entityManager->flush();

            $this->addFlash('success', 'Le fichier a été télécharger avec succès.');

            return $this->redirectToRoute('app_upload_file_new', ['id' => $id->getId(), 'type' => $type], Response::HTTP_SEE_OTHER);
        }

        return $this->render('upload_file/new.html.twig', [
            'upload_file' => $uploadFile,
            'form' => $form,
        ]);
    }


    #[Route('/{id}', name: 'app_upload_file_delete', methods: ['POST'])]
    public function delete(Request $request, UploadFile $uploadFile, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$uploadFile->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($uploadFile);
            $entityManager->flush();
        }

        $this->addFlash('success', 'Le fichier a été supprimer avec succès.');
        $route = $request->headers->get('referer');

        return $this->redirect($route);
    }

    /**
     * Controle la validité des paramètres de la requête
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @throws \LogicException
     * @return void
     */
    private function checkRequestValidity(Request $request): array
    {
        $methode = $request->getMethod();

        //check que la query contient bien les paramètres entity, id et type
        if($request->query->has('entityClass') == false || $request->query->has('entityId') == false || $request->query->has('type') == false) {
            throw new \LogicException('Erreur dans la requête');
        }else{
            //check que les paramètres entity, id et type sont bien définis
            $entity = $request->query->get('entityClass');
            $id = $request->query->get('entityId');
            $type = $request->query->get('type');
            //si l'entité n'est pas property, on renvoie une erreur
            if($entity != 'Property'){
                throw new \LogicException('Entité non prise en charge');
            }
            //on recherche l'entité avec son id et sa class pour vérifier qu'elle existe et qu'on a le droit d'y accéder
            $repository = $this->entityManager->getRepository('App\\Entity\\'.$entity);
            $entityLoaded = $repository->findOneBy(['id' => $id]);
            if($entityLoaded == null){
                throw new \LogicException('Erreur ID');
            }
            if($type != 'image' && $type != 'document'){
                throw new \LogicException('Type non pris en charge');
            }

            //si la requete est de type POST (donc envoyé)
            if($methode == 'POST'){
                $extension = $request->files->get('upload_file')['file']->guessExtension();
                //si le type est image, on verifie que l'extension est bonne
                if ($type === 'image' && !in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                    throw new \LogicException('Extension non prise en charge pour les images');
                }
            }

        }

        if($entity == 'property'){
            $propertyCheck = $this->accessControlService->canAccessProperty($this->getUser(), $entityLoaded, AccessRoleEnum::MEMBER);
            if (!$propertyCheck) {
                throw $this->createAccessDeniedException('Vous n’avez pas accès à cette propriété.');
            }
        }

        return ['entityClass' => $entity, 'entityId' => $id, 'type' => $type, 'loadedEntity' => $entityLoaded];
    }
}
