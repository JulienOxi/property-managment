<?php

namespace App\Controller;

use App\Entity\Property;
use App\Entity\UploadFile;
use App\Enum\AccessRoleEnum;
use App\Service\DateService;
use App\Service\EnumService;
use App\Service\PropertyService;
use BadRequestHttpException;
use App\Enum\TransactionEnum;
use App\Entity\FinancialEntry;
use App\Enum\FinancialCategoryEnum;
use App\Form\FinancialEntryNewType;
use App\Form\TransactionFilterType;
use App\Form\FinancialEntryEditType;
use App\Repository\PropertyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use App\Repository\FinancialEntryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/financialentry')]
final class FinancialEntryController extends AbstractController
{
    #[Route(name: 'app_financial_entry_index', methods: ['GET', 'POST'])]
    public function index(Request $request, FinancialEntryRepository $financialEntryRepository, PropertyRepository $propertyRepository, PaginatorInterface $paginator, CsrfTokenManagerInterface $csrfTokenManager): Response
    {
        //formulaire de recherche avec récupération des paramètres de l'url pour préremplire le formulaire
        $caterories = explode(':', $request->query->get('category'));//on récupère les catégories
        $caterories = array_map(fn($category) => FinancialCategoryEnum::fromName($category), $caterories); //on les transforme en enum
        
        // Récupérer les propriétés accessibles par l'utilisateur
        $accessibleProperties = $propertyRepository->findAccessibleProperties($this->getUser(), [AccessRoleEnum::MEMBER, AccessRoleEnum::OWNER]);

        // Créer le formulaire
        $form = $this->createForm(TransactionFilterType::class, [
            'property' => $request->query->get('property') ? $propertyRepository->find($request->query->get('property')) : null,
            'category' => $request->query->get('category') ? $caterories : null,
            'type' => $request->query->get('type') ? TransactionEnum::fromName($request->query->get('type')) : null,
        ], [
            'accessible_properties' => $accessibleProperties, // Passer les propriétés accessibles
        ]);

        $form->handleRequest($request);

        //si le formulaire est soumis et valide on redirige vers la page avec les paramètres dans l'url
        if ($form->isSubmitted() && $form->isValid()) {

            $properties = $form->get('property')->getData();
            $categories = $form->get('category')->getData();
            $types = $form->get('type')->getData();

            $categories = array_map(fn($category) => $category->name, $categories);
            $categories = implode(':', $categories);

                    // Redirige avec les paramètres dans l'URL
        return $this->redirectToRoute('app_financial_entry_index', [
            'property' => $properties ? $properties->getId() : null,
            'category' => $categories, // Convertir tableau -> chaîne
            'type' => $types->name,
        ]);

        }

        //on récupère les paramètres de l'url pour les passer à la requête
        $request->query->get('property') ? $property = $request->query->get('property') : $property = null;
        if($request->query->get('category')) {
            $caterories = explode(':', $request->query->get('category'));//on récupère les catégories
            $caterories = array_map(fn($category) => FinancialCategoryEnum::fromName($category), $caterories); //on les transforme en enum
        }
        $request->query->get('type') ? $type = $request->query->get('type') : $type = null;
        if($property && $caterories && $type){
            $query = $financialEntryRepository->findByPropertiesAndCategoriesAndTypes($this->getUser(), AccessRoleEnum::OWNER, [$propertyRepository->find($property)], $caterories, TransactionEnum::fromName($type));
        }else{
            $query = $financialEntryRepository->findByPropertiesAndCategoriesAndTypes($this->getUser(), AccessRoleEnum::OWNER, $accessibleProperties, [], null, 100);
        }

        $financialEntries = $paginator->paginate(
            $query,
            $request->query->get('page') ?? 1, // Numéro de page
            10 // Nombre de résultats par page
        );

        return $this->render('financial_entry/index.html.twig', [
            'financial_entries' => $financialEntries,
            'properties' => $accessibleProperties,
            'form' => $form->createView(),
            'queryParams' => $request->query->all(),
        ]);
    }

    #[Route('/new', name: 'app_financial_entry_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, PropertyService $propertyService): Response
    {
        $financialEntry = new FinancialEntry();

        //validation de la description
        $description = $request->query->get('description') ? $request->query->get('description') : null;
        if($description){
            if (!preg_match('/^[\p{L}\p{N}\s.,!?\-()\'"]+$/u', $description)) {
                $this->addFlash('danger', 'La description contient des caractères non autorisés.');
                return $this->redirectToRoute('app_financial_entry_index', [], Response::HTTP_SEE_OTHER);
            }
        }

        $paidAt = $request->query->get('paidAt') ? $request->query->get('paidAt') : null;
        if($paidAt){
            $paidAt = \DateTimeImmutable::createFromFormat('Y-m-d', $paidAt);

            if ($paidAt === false) {
                $this->addFlash('danger', 'La date n\'est pas valide.');
                return $this->redirectToRoute('app_financial_entry_index', [], Response::HTTP_SEE_OTHER);
            }
        }


        // On Set le type si il est renseigné dans la route
        $request->query->get('type') ? $financialEntry->setType(TransactionEnum::fromName($request->query->get('type'))) : null;
        $request->query->get('property') ? $financialEntry->setProperty($entityManager->getRepository(Property::class)->find($request->query->get('property'))) : null;
        $request->query->get('category') ? $financialEntry->setCategory(FinancialCategoryEnum::fromName($request->query->get('category'))) : null;
        $description ? $financialEntry->setDescription($description) : null;
        $paidAt ? $financialEntry->setPaidAt($paidAt) : null;

        $form = $this->createForm(FinancialEntryNewType::class, $financialEntry);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $financialEntry->setCreatedBy($this->getUser())
                ->setBank($propertyService->getActualMortgages($financialEntry->getProperty())[0]->getBank());
            $entityManager->persist($financialEntry);
            $entityManager->flush();

            //Si l'utilisateur selectionne un fichier en rapport avec la dépense on le met à jour
            if($form['uploadFile']->getData() !== null){
                $uploadFile = $form['uploadFile']->getData();
                $uploadFile
                    ->setUpdatedBy($this->getUser())
                    ->setEntityClass(FinancialEntry::class)
                    ->setEntityId($financialEntry->getId());
                $entityManager->flush();
            };

            $this->addFlash('success', 'La transaction a bien été ajouté');

            return $this->redirectToRoute('app_financial_entry_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('financial_entry/new.html.twig', [
            'financial_entry' => $financialEntry,
            'form' => $form,
            'properties' => $entityManager->getRepository(Property::class)->findAccessibleProperties($this->getUser(), [AccessRoleEnum::MEMBER, AccessRoleEnum::OWNER]),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_financial_entry_edit', methods: ['GET', 'POST'], requirements: ['id' => Requirement::POSITIVE_INT])]
    public function edit(Request $request, FinancialEntry $financialEntry, EntityManagerInterface $entityManager, RequestStack $requestStack): Response
    {
        $session = $request->getSession(); 
        
        $form = $this->createForm(FinancialEntryEditType::class, $financialEntry);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //Si l'utilisateur selectionne un fichier en rapport avec la dépense on le met à jour
            if($form['uploadFile']->getData() !== null){
                $uploadFile = $form['uploadFile']->getData();
                $uploadFile
                    ->setUpdatedBy($this->getUser())
                    ->setEntityClass(FinancialEntry::class)
                    ->setEntityId($financialEntry->getId());
            };
            $entityManager->flush();
            
            $this->addFlash('success','Modification effectuée');

            $referer = $session->get('referer', null); // Rediriger vers le referer si disponible, sinon vers une route par défaut 
            if ($referer) 
            { 
                    return new RedirectResponse($referer); 
            } else { 
                return $this->redirectToRoute('app_financial_entry_index', [], Response::HTTP_SEE_OTHER);
            }
        }else{
                // Enregistrer le referer dans la session 
                $request = $requestStack->getCurrentRequest(); 
                $referer = $request->headers->get('referer');
                $session->set('referer', $referer);
        }

        return $this->render('financial_entry/edit.html.twig', [
            'financial_entry' => $financialEntry,
            'form' => $form,
            'uploaded_file' => $entityManager->getRepository(UploadFile::class)->findOneBy(['entityClass' => FinancialEntry::class, 'entityId' => $financialEntry->getId()]),
        ]);
    }

    #[Route('/{id}', name: 'app_financial_entry_delete', methods: ['POST'], requirements: ['id' => Requirement::POSITIVE_INT])]
    public function delete(Request $request, FinancialEntry $financialEntry, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$financialEntry->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($financialEntry);
            $entityManager->flush();
        }
        $this->addFlash('success', 'La transaction a été supprimée');
        return $this->redirectToRoute('app_financial_entry_index', [], Response::HTTP_SEE_OTHER);
    }

}
